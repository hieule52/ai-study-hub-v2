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
            SELECT e.progress_percent, e.enrolled_at, c.* 
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.user_id = :user_id AND c.deleted_at IS NULL
            ORDER BY e.enrolled_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về mảng array data để tiện render frontend
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
        return (bool)$stmt->fetch();
    }

    public function findStudentsByTeacher(int $teacherId): array
    {
        // Join enrollments with users and courses, filtering by teacher_id
        $stmt = $this->db->prepare("
            SELECT u.id as user_id, u.username, u.email, 
                   c.id as course_id, c.title as course_title, 
                   e.progress_percent, e.enrolled_at
            FROM enrollments e
            JOIN users u ON e.user_id = u.id
            JOIN courses c ON e.course_id = c.id
            WHERE c.teacher_id = :teacher_id AND c.deleted_at IS NULL
            ORDER BY e.enrolled_at DESC
        ");
        $stmt->execute(['teacher_id' => $teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLearningStats(int $userId): array
    {
        // Thống kê số bài học ('lesson_progress') hoàn thành trong 7 ngày gần nhất
        // Mục đích: Phục vụ cho biểu đồ
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

        // Chuyển Data Database sang mảng 7 ngày liên tục kể cả ngày không học (count = 0)
        $stats = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = date('Y-m-d', strtotime("-$i days"));
            $stats[$dateStr] = 0; // Mặc định là 0 bài
        }

        foreach ($rows as $row) {
            if (isset($stats[$row['date_val']])) {
                $stats[$row['date_val']] = (int)$row['count'];
            }
        }

        return $stats;
    }
}
