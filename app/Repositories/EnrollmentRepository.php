<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Course;
use PDO;

class EnrollmentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findEnrolledCoursesByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT e.progress_percent, e.enrolled_at, c.*,
                   u.username as teacher_name, u.email as teacher_email, u.avatar as teacher_avatar, u.last_seen as teacher_last_seen
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            LEFT JOIN users u ON c.teacher_id = u.id
            WHERE e.user_id = :user_id AND c.deleted_at IS NULL
            ORDER BY e.enrolled_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function enroll(int $userId, int $courseId): bool
    {
        if ($this->checkEnrollment($userId, $courseId)) {
            return false; // Đã enrolled
        }

        $stmt = $this->db->prepare("
            INSERT INTO enrollments (user_id, course_id, progress_percent) 
            VALUES (:user_id, :course_id, 0)
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'course_id' => $courseId
        ]);
    }

    public function checkEnrollment(int $userId, int $courseId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM enrollments WHERE user_id = :user_id AND course_id = :course_id");
        $stmt->execute(['user_id' => $userId, 'course_id' => $courseId]);
        return $stmt->fetchColumn() !== false;
    }

    public function updateProgress(int $userId, int $courseId): int
    {
        // Total lessons in course
        $stmtTotal = $this->db->prepare("
            SELECT COUNT(l.id) as total
            FROM lessons l
            JOIN chapters c ON l.chapter_id = c.id
            WHERE c.course_id = :course_id AND l.deleted_at IS NULL
        ");
        $stmtTotal->execute(['course_id' => $courseId]);
        $total = (int)$stmtTotal->fetchColumn();

        if ($total === 0) return 0;

        // Completed lessons by user
        $stmtCompleted = $this->db->prepare("
            SELECT COUNT(lp.lesson_id) as completed
            FROM lesson_progress lp
            JOIN lessons l ON lp.lesson_id = l.id
            JOIN chapters c ON l.chapter_id = c.id
            WHERE lp.user_id = :user_id 
              AND lp.is_completed = 1 
              AND c.course_id = :course_id
              AND l.deleted_at IS NULL
        ");
        $stmtCompleted->execute(['user_id' => $userId, 'course_id' => $courseId]);
        $completed = (int)$stmtCompleted->fetchColumn();

        $percent = (int)round(($completed / $total) * 100);

        // Update enrollment
        $stmtUpdate = $this->db->prepare("
            UPDATE enrollments 
            SET progress_percent = :percent 
            WHERE user_id = :user_id AND course_id = :course_id
        ");
        $stmtUpdate->execute([
            'percent' => $percent,
            'user_id' => $userId,
            'course_id' => $courseId
        ]);

        return $percent;
    }

    public function findStudentsByTeacher(int $teacherId): array
    {
        // Join enrollments with users and courses, filtering by teacher_id
        // Exclude locked (banned) and soft-deleted students
        $stmt = $this->db->prepare("
            SELECT u.id as user_id, u.username, u.email, u.avatar, u.last_seen,
                   c.id as course_id, c.title as course_title, 
                   e.progress_percent, e.enrolled_at
            FROM enrollments e
            JOIN users u ON e.user_id = u.id
            JOIN courses c ON e.course_id = c.id
            WHERE c.teacher_id = :teacher_id
              AND u.status = 'active'
              AND u.deleted_at IS NULL
            ORDER BY e.enrolled_at DESC
        ");
        $stmt->execute(['teacher_id' => $teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLearningStats(int $userId): array
    {
        // 1. Thống kê biểu đồ (7 ngày gần nhất)
        $stmt = $this->db->prepare("
            SELECT DATE(completed_at) as date_val, COUNT(id) as count
            FROM lesson_progress
            WHERE user_id = :user_id 
              AND is_completed = 1 
              AND completed_at >= DATE(NOW() - INTERVAL 6 DAY)
            GROUP BY DATE(completed_at)
            ORDER BY date_val ASC
        ");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = date('Y-m-d', strtotime("-$i days"));
            $chartData[$dateStr] = 0;
        }
        foreach ($rows as $row) {
            if (isset($chartData[$row['date_val']])) {
                $chartData[$row['date_val']] = (int)$row['count'];
            }
        }

        // 2. Tính tỷ lệ hoàn thành trung bình (completion_rate)
        $stmtRate = $this->db->prepare("SELECT AVG(progress_percent) FROM enrollments WHERE user_id = ?");
        $stmtRate->execute([$userId]);
        $completionRate = (int)$stmtRate->fetchColumn();

        // 3. Đếm số chứng chỉ
        $stmtCerts = $this->db->prepare("SELECT COUNT(*) FROM certificates WHERE user_id = ?");
        $stmtCerts->execute([$userId]);
        $certsCount = (int)$stmtCerts->fetchColumn();

        // 4. Tính chuỗi ngày học (streak) - đơn giản hóa: số ngày có học liên tiếp tính từ hôm nay/hôm qua
        // Ở đây ta trả về số ngày có lesson_progress trong 30 ngày qua cho đơn giản (logic streak thực tế phức tạp hơn)
        $stmtStreak = $this->db->prepare("
            SELECT COUNT(DISTINCT DATE(completed_at)) 
            FROM lesson_progress 
            WHERE user_id = ? AND is_completed = 1
        ");
        $stmtStreak->execute([$userId]);
        $streak = (int)$stmtStreak->fetchColumn();

        return [
            'chart'              => $chartData,
            'completion_rate'    => $completionRate,
            'certificates_count' => $certsCount,
            'streak'             => $streak
        ];
    }

    /**
     * Lấy phần trăm tiến độ của một khóa học cụ thể
     */
    public function getProgressPercent(int $userId, int $courseId): int
    {
        $stmt = $this->db->prepare("
            SELECT progress_percent 
            FROM enrollments 
            WHERE user_id = :uid AND course_id = :cid 
            LIMIT 1
        ");
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['progress_percent'] : 0;
    }

    public function getAllEnrollments(): array
    {
        $stmt = $this->db->query("
            SELECT e.id, e.enrolled_at, e.progress_percent,
                   u.username as student_name, u.email as student_email,
                   c.title as course_title, c.price as course_price,
                   t.username as teacher_name
            FROM enrollments e
            JOIN users u ON e.user_id = u.id
            JOIN courses c ON e.course_id = c.id
            LEFT JOIN users t ON c.teacher_id = t.id
            WHERE c.deleted_at IS NULL
            ORDER BY e.enrolled_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
