<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class ChangeTrackingService
{
    private static ?PDO $db = null;

    private static function getDb(): PDO
    {
        if (self::$db === null) {
            self::$db = Database::connect();
        }
        return self::$db;
    }

    /**
     * Ghi lại nhật ký thay đổi và gửi thông báo kiểm duyệt cho Admin.
     * 
     * ĐÂY LÀ CƠ QUAN DUY NHẤT xử lý:
     *   1. Phân tích trường thay đổi
     *   2. Ghi log vào course_change_logs
     *   3. Tự động đổi status khóa học sang pending_reapproval (nếu cốt lõi)
     *   4. Gửi thông báo vào admin_notifications
     *   5. Gửi thông báo vào notifications (cho từng admin user)
     * 
     * KHÔNG GỌI requireReapproval() ở bất kỳ đâu khác — tránh race condition.
     */
    public static function trackChange(
        int $courseId,
        string $entityType,
        int $entityId,
        string $actionType,
        ?array $oldState,
        ?array $newState,
        int $userId
    ): void {
        $db = self::getDb();

        // Lấy thông tin khóa học (trạng thái hiện tại trước khi thực hiện thay đổi)
        $stmtCourse = $db->prepare("SELECT title, status, teacher_id FROM courses WHERE id = ?");
        $stmtCourse->execute([$courseId]);
        $course = $stmtCourse->fetch(PDO::FETCH_ASSOC);
        if (!$course) return;
        $courseTitle = $course['title'];
        $courseStatus = $course['status'];
        $teacherId = (int)$course['teacher_id'];

        // KHÔNG theo dõi thay đổi hay tạo thông báo nếu khóa học vẫn là Bản nháp (draft) hoặc Chờ duyệt lần đầu (pending)
        if ($courseStatus === 'draft' || $courseStatus === 'pending') {
            return;
        }

        // 1. Phân tích các trường thay đổi
        $changedFields = [];
        if ($actionType === 'update' && is_array($oldState) && is_array($newState)) {
            foreach ($newState as $key => $val) {
                if (array_key_exists($key, $oldState)) {
                    // So sánh giá trị (bỏ qua so sánh kiểu nghiêm ngặt để tránh false-positives giữa string và numeric)
                    if ($oldState[$key] != $val) {
                        $changedFields[] = $key;
                    }
                }
            }
            if (empty($changedFields)) {
                return; // Không có thay đổi thực tế nào
            }
        } elseif ($actionType === 'create') {
            $changedFields = is_array($newState) ? array_keys($newState) : [];
        } elseif ($actionType === 'delete') {
            $changedFields = is_array($oldState) ? array_keys($oldState) : [];
        }

        // 2. Lưu vào bảng course_change_logs
        $stmtLog = $db->prepare("
            INSERT INTO course_change_logs (course_id, entity_type, entity_id, action_type, changed_fields, old_snapshot, new_snapshot, performed_by)
            VALUES (:cid, :etype, :eid, :atype, :cfields, :old_snap, :new_snap, :by)
        ");
        $stmtLog->execute([
            'cid'     => $courseId,
            'etype'   => $entityType,
            'eid'     => $entityId,
            'atype'   => $actionType,
            'cfields' => json_encode($changedFields),
            'old_snap'=> $oldState ? json_encode($oldState) : null,
            'new_snap'=> $newState ? json_encode($newState) : null,
            'by'      => $userId
        ]);
        $changeLogId = $db->lastInsertId();

        // Lấy thông tin người thực hiện
        $stmtUser = $db->prepare("SELECT username, role FROM users WHERE id = ?");
        $stmtUser->execute([$userId]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        $userName = $user['username'] ?? "Giảng viên #{$userId}";

        // 3. Phân loại thay đổi Cốt lõi (Critical) vs Không cốt lõi (Non-Critical)
        $isCritical = false;
        $sectionName = "";

        if ($entityType === 'lesson') {
            $sectionName = "Bài học";
            if ($actionType === 'create' || $actionType === 'delete') {
                $isCritical = true;
            } else {
                // Các trường bài học được coi là cốt lõi (bao gồm cả AI tutor context)
                $criticalLessonFields = [
                    'title', 'content_type', 'lesson_type', 
                    'video_transcript', 'content', 'video_filename', 'video_url',
                    'strict_ai_mode', 'teacher_notes', 
                    'enable_auto_summary', 'enable_auto_keywords', 'enable_auto_context'
                ];
                foreach ($changedFields as $field) {
                    if (in_array($field, $criticalLessonFields)) {
                        $isCritical = true;
                        break;
                    }
                }
            }
        } elseif ($entityType === 'quiz') {
            $sectionName = "Bài kiểm tra";
            // Tất cả thay đổi về Quiz / Câu hỏi / Đáp án đều là Cốt lõi
            $isCritical = true;
        } elseif ($entityType === 'chapter') {
            $sectionName = "Chương học";
            // Chương học bị thêm mới, xóa bỏ hoặc đổi tên đều thay đổi cấu trúc khóa học
            $isCritical = true;
        } elseif ($entityType === 'course') {
            $sectionName = "Thông tin khóa học";
            // Đổi tiêu đề hoặc mô tả cốt lõi
            if (in_array('title', $changedFields) || in_array('description', $changedFields)) {
                $isCritical = true;
            }
        }

        // 4. Cơ chế tự động chuyển trạng thái khóa học (Auto Course Status Logic)
        // CHỈ chuyển nếu khóa học ĐANG approved — đây là điểm duy nhất thay đổi status
        $statusAutoTriggered = false;
        if ($isCritical && $courseStatus === 'approved') {
            $stmtUpdateStatus = $db->prepare("UPDATE courses SET status = 'pending_reapproval' WHERE id = ? AND status = 'approved'");
            $stmtUpdateStatus->execute([$courseId]);
            if ($stmtUpdateStatus->rowCount() > 0) {
                $statusAutoTriggered = true;

                // Gửi thông báo riêng vào bảng notifications cho từng admin
                // (để admin thấy trong dropdown notification cá nhân)
                $admins = $db->query("SELECT id FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_COLUMN);
                $notifStmt = $db->prepare("INSERT INTO notifications (user_id, type, title, content) VALUES (:uid, 'warning', :title, :msg)");
                foreach ($admins as $adminId) {
                    $notifStmt->execute([
                        'uid' => $adminId,
                        'title' => "Khóa học cần duyệt lại",
                        'msg' => "Khóa học '{$courseTitle}' đã bị chỉnh sửa bởi giảng viên {$userName} và đang chờ bạn xét duyệt lại."
                    ]);
                }

                // Thông báo cho teacher: khóa học đã chuyển trạng thái
                $teacherNotifStmt = $db->prepare("INSERT INTO notifications (user_id, type, title, content) VALUES (:uid, 'warning', :title, :msg)");
                $teacherNotifStmt->execute([
                    'uid' => $teacherId,
                    'title' => "⚠️ Khóa học đang chờ duyệt lại",
                    'msg' => "Khóa học '{$courseTitle}' đã tự động chuyển sang trạng thái 'Chờ duyệt lại' vì bạn đã chỉnh sửa nội dung cốt lõi ({$sectionName})."
                ]);
            }
        }

        // 5. Gửi thông báo kiểm duyệt vào bảng admin_notifications (cho Moderation Center)
        $priority = $isCritical ? 'high' : 'low';
        $title = "";
        $message = "";

        if ($isCritical) {
            if ($statusAutoTriggered) {
                $title = "⚠️ Khóa học cần phê duyệt lại";
                $message = "Giảng viên {$userName} đã chỉnh sửa nội dung cốt lõi của khóa học '{$courseTitle}' ({$sectionName}). Trạng thái khóa học đã tự động chuyển sang 'Chờ duyệt lại'.";
            } else {
                $title = "⚠️ Thay đổi cốt lõi: {$sectionName}";
                $message = "Giảng viên {$userName} đã chỉnh sửa nội dung cốt lõi ({$sectionName}) trong khóa học '{$courseTitle}' (trạng thái hiện tại: {$courseStatus}).";
            }
        } else {
            if ($actionType === 'create') {
                $title = "🆕 Tạo mới {$sectionName}";
                $message = "Giảng viên {$userName} đã tạo mới {$sectionName} trong khóa học '{$courseTitle}'.";
            } elseif ($actionType === 'delete') {
                $title = "❌ Xóa {$sectionName}";
                $message = "Giảng viên {$userName} đã xóa {$sectionName} trong khóa học '{$courseTitle}'.";
            } else {
                $title = "✏️ Cập nhật {$sectionName}";
                $message = "Giảng viên {$userName} đã cập nhật {$sectionName} trong khóa học '{$courseTitle}'.";
            }
        }

        $stmtNotif = $db->prepare("
            INSERT INTO admin_notifications (course_id, type, title, message, data, priority, is_read)
            VALUES (:cid, :type, :title, :msg, :data, :priority, 0)
        ");
        $stmtNotif->execute([
            'cid'      => $courseId,
            'type'     => $isCritical ? 'critical_update' : 'non_critical_update',
            'title'    => $title,
            'msg'      => $message,
            'data'     => json_encode([
                'change_log_id' => $changeLogId,
                'entity_type'   => $entityType,
                'entity_id'     => $entityId,
                'action_type'   => $actionType,
                'changed_fields'=> $changedFields,
                'status_changed'=> $statusAutoTriggered
            ]),
            'priority' => $priority
        ]);
    }
}
