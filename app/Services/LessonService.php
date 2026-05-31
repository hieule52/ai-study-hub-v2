<?php

namespace App\Services;

use App\Repositories\LessonRepository;
use App\Repositories\EnrollmentRepository;
use Exception;

class LessonService
{
    private LessonRepository $lessonRepo;

    public function __construct()
    {
        $this->lessonRepo = new LessonRepository();
    }

    /**
     * Lấy giáo trình khoá học với trạng thái khoá/mở của từng bài học.
     * Học viên chỉ được mở bài hiện tại và bài đã hoàn thành.
     * Các bài phía sau đều bị khoá.
     */
    public function getCourseCurriculum(int $courseId, string $userRole = 'student', int $userId = 0): array
    {
        $chapters = $this->lessonRepo->findChaptersByCourse($courseId);
        if (empty($chapters)) return [];

        // Check pending_reapproval status
        $db = \App\Core\Database::connect();
        $stmtStatus = $db->prepare("SELECT status FROM courses WHERE id = ?");
        $stmtStatus->execute([$courseId]);
        $courseStatus = $stmtStatus->fetchColumn();

        // Lấy tất cả lessons trong 1 query
        $chapterIds = array_column($chapters, 'id');
        $allLessons = $this->lessonRepo->findLessonsByChapterIds($chapterIds);

        if ($courseStatus === 'pending_reapproval' && $userRole !== 'teacher' && $userRole !== 'admin') {
            $allLessons = $this->applyPendingReapprovalFilter($db, $courseId, $chapters, $allLessons, $chapters);
        }

        // Lấy progress của user trong khóa học (1 query thay vì N queries)
        $progressMap = [];
        if ($userId > 0 && $userRole === 'student') {
            $progressMap = $this->lessonRepo->findProgressByCourse($userId, $courseId);
        }

        // Group lessons by chapter
        $lessonsByChapter = [];
        foreach ($allLessons as $lesson) {
            $lessonsByChapter[$lesson['chapter_id']][] = $lesson;
        }

        // Tính trạng thái khoá/mở dựa vào logic tuần tự
        // Bài đầu tiên luôn mở; bài tiếp theo chỉ mở khi bài trước đã completed
        $prevCompleted = true; // Bài đầu tiên không cần điều kiện

        foreach ($chapters as &$chapter) {
            $lessons = $lessonsByChapter[$chapter['id']] ?? [];
            foreach ($lessons as &$lesson) {
                $lessonId = (int)$lesson['id'];
                $prog = $progressMap[$lessonId] ?? null;
                $isCompleted = $prog && (int)$prog['is_completed'] === 1;
                $videoProgress = $prog ? (int)$prog['video_progress'] : 0;
                $textProgress  = $prog ? (int)$prog['text_progress']  : 0;

                // Tuần tự: mở nếu bài trước đã hoàn thành (hoặc chính bài đó đã hoàn thành)
                $isUnlocked = $prevCompleted || $isCompleted;

                $lesson['is_completed']   = $isCompleted ? 1 : 0;
                $lesson['video_progress'] = $videoProgress;
                $lesson['text_progress']  = $textProgress;
                $lesson['is_locked']      = (!$isUnlocked && $userRole === 'student') ? 1 : 0;
                $lesson['completed_at']   = $prog['completed_at'] ?? null;

                // Bài tiếp theo chỉ mở khi bài này đã completed
                $prevCompleted = $isCompleted;
            }
            unset($lesson);
            $chapter['lessons'] = $lessons;
        }
        unset($chapter);

        return $chapters;
    }

    /**
     * Lấy thông tin chi tiết bài học, kèm trạng thái progress của student.
     */
    public function getLessonDetail(int $lessonId, string $userRole = 'student', int $userId = 0): array
    {
        if ($userId > 0 && $userRole === 'student') {
            $lesson = $this->lessonRepo->findLessonWithProgress($lessonId, $userId);
        } else {
            $lesson = $this->lessonRepo->findLessonById($lessonId);
        }

        if (!$lesson) {
            throw new Exception("Không tìm thấy bài học này.");
        }

        // Áp dụng snapshot nếu pending_reapproval
        $db = \App\Core\Database::connect();
        $stmtStatus = $db->prepare("
            SELECT c.status, c.id as course_id
            FROM courses c
            JOIN chapters ch ON c.id = ch.course_id
            WHERE ch.id = ?
        ");
        $stmtStatus->execute([$lesson['chapter_id']]);
        $courseInfo = $stmtStatus->fetch(\PDO::FETCH_ASSOC);

        if ($courseInfo && $courseInfo['status'] === 'pending_reapproval' && $userRole !== 'teacher' && $userRole !== 'admin') {
            $stmtLog = $db->prepare("
                SELECT * FROM course_change_logs
                WHERE course_id = ? AND entity_type = 'lesson' AND entity_id = ? AND action_type = 'update'
                ORDER BY id ASC LIMIT 1
            ");
            $stmtLog->execute([$courseInfo['course_id'], $lessonId]);
            $log = $stmtLog->fetch(\PDO::FETCH_ASSOC);
            if ($log && $log['old_snapshot']) {
                $oldSnap = json_decode($log['old_snapshot'], true);
                foreach ($oldSnap as $k => $v) {
                    $lesson[$k] = $v;
                }
            }
        }

        return $lesson;
    }

    /**
     * Cập nhật tiến độ video/text của học viên.
     * Trả về kết quả kèm trạng thái hoàn thành và progress khóa học.
     */
    public function updateProgress(int $userId, int $lessonId, int $videoProgress, int $textProgress): array
    {
        $lesson = $this->lessonRepo->findLessonById($lessonId);
        if (!$lesson) {
            throw new Exception("Lesson không tồn tại.");
        }

        // Clamp values 0-100
        $videoProgress = max(0, min(100, $videoProgress));
        $textProgress  = max(0, min(100, $textProgress));

        $progressState = $this->lessonRepo->updateLessonProgress($userId, $lessonId, $videoProgress, $textProgress);
        $justCompleted = (int)$progressState['is_completed'] === 1;

        // Recalculate course progress
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT course_id FROM chapters WHERE id = :id");
        $stmt->execute(['id' => $lesson['chapter_id']]);
        $courseId = (int) $stmt->fetchColumn();

        $courseResult = ['progress_percent' => 0, 'course_completed' => false, 'certificate_issued' => false, 'needs_review' => false];
        if ($courseId) {
            $courseService = new CourseService();
            $courseResult = $courseService->checkAndProcessCourseCompletion($userId, $courseId);
        }

        return array_merge($progressState, [
            'just_completed' => $justCompleted,
            'course_id'      => $courseId,
        ], $courseResult);
    }

    /**
     * Đánh dấu hoàn thành bài học thủ công (nút "Đánh dấu Đã Học").
     * Bắt buộc kiểm tra công thức hoàn thành LMS: Video ≥ 80% AND Text ≥ 80% AND Quiz đã vượt qua.
     */
    public function markLessonCompleted(int $userId, int $lessonId): array
    {
        $lesson = $this->lessonRepo->findLessonById($lessonId);
        if (!$lesson) {
            throw new Exception("Lesson không tồn tại.");
        }

        // Kiểm tra công thức hoàn thành tích hợp trước khi cho phép đánh dấu
        $canComplete = $this->lessonRepo->evaluateLessonCompletion($userId, $lessonId);
        if (!$canComplete) {
            throw new Exception("Bạn chưa đáp ứng đủ điều kiện hoàn thành bài học. Hãy xem đủ 80% video, đọc đủ 80% nội dung và vượt qua bài kiểm tra (nếu có).");
        }

        $this->lessonRepo->markProgress($userId, $lessonId);

        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT course_id FROM chapters WHERE id = :id");
        $stmt->execute(['id' => $lesson['chapter_id']]);
        $courseId = (int) $stmt->fetchColumn();

        $courseResult = ['progress_percent' => 0, 'course_completed' => false, 'certificate_issued' => false, 'needs_review' => false];
        if ($courseId) {
            $courseService = new CourseService();
            $courseResult = $courseService->checkAndProcessCourseCompletion($userId, $courseId);
        }

        return array_merge(['is_completed' => 1, 'course_id' => $courseId], $courseResult);
    }

    // ─── Private helper for pending_reapproval filtering ────────────────────

    private function applyPendingReapprovalFilter($db, int $courseId, array &$chapters, array $allLessons, array $origChapters): array
    {
        $stmtLogs = $db->prepare("SELECT * FROM course_change_logs WHERE course_id = ? ORDER BY id ASC");
        $stmtLogs->execute([$courseId]);
        $logs = $stmtLogs->fetchAll(\PDO::FETCH_ASSOC);

        $pendingChapters = [];
        $pendingLessons  = [];

        foreach ($logs as $log) {
            $etype = $log['entity_type'];
            $eid   = (int)$log['entity_id'];
            $atype = $log['action_type'];
            $oldSnap = $log['old_snapshot'] ? json_decode($log['old_snapshot'], true) : null;

            if ($etype === 'chapter' && !isset($pendingChapters[$eid])) {
                $pendingChapters[$eid] = ['action' => $atype, 'snapshot' => $oldSnap];
            } elseif ($etype === 'lesson' && !isset($pendingLessons[$eid])) {
                $pendingLessons[$eid] = ['action' => $atype, 'snapshot' => $oldSnap];
            }
        }

        // Reconstruct chapters
        $activeChapters = [];
        foreach ($chapters as $ch) {
            $chId = (int)$ch['id'];
            if (isset($pendingChapters[$chId])) {
                $info = $pendingChapters[$chId];
                if ($info['action'] === 'create') continue;
                if ($info['action'] === 'update' && $info['snapshot']) {
                    foreach ($info['snapshot'] as $k => $v) $ch[$k] = $v;
                }
            }
            $activeChapters[] = $ch;
        }
        foreach ($pendingChapters as $chId => $info) {
            if ($info['action'] === 'delete' && $info['snapshot']) $activeChapters[] = $info['snapshot'];
        }
        usort($activeChapters, fn($a, $b) => ($a['order_index'] ?? 0) - ($b['order_index'] ?? 0));
        $chapters = $activeChapters;

        // Reconstruct lessons
        $activeLessons = [];
        foreach ($allLessons as $lesson) {
            $lesId = (int)$lesson['id'];
            if (isset($pendingLessons[$lesId])) {
                $info = $pendingLessons[$lesId];
                if ($info['action'] === 'create') continue;
                if ($info['action'] === 'update' && $info['snapshot']) {
                    foreach ($info['snapshot'] as $k => $v) $lesson[$k] = $v;
                }
            }
            $activeLessons[] = $lesson;
        }
        foreach ($pendingLessons as $lesId => $info) {
            if ($info['action'] === 'delete' && $info['snapshot']) $activeLessons[] = $info['snapshot'];
        }
        usort($activeLessons, fn($a, $b) => ($a['order_index'] ?? 0) - ($b['order_index'] ?? 0));

        return $activeLessons;
    }
}
