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
        // Kiểm tra xem đã enroll chưa
        $stmt = $this->db->prepare("SELECT id FROM enrollments WHERE user_id = :user_id AND course_id = :course_id");
        $stmt->execute(['user_id' => $userId, 'course_id' => $courseId]);
        if ($stmt->fetch()) {
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
}
