<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * LearningPathRepository
 * CRUD cho hệ thống lộ trình học (Learning Path / Roadmap).
 * Một lộ trình chứa nhiều khóa học sắp theo thứ tự.
 */
class LearningPathRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // ==========================================
    // LEARNING PATH CRUD
    // ==========================================

    /**
     * Lấy tất cả lộ trình đã publish
     */
    public function findAllPublished(): array
    {
        $sql = "SELECT lp.*, u.username as creator_name,
                       (SELECT COUNT(*) FROM learning_path_courses lpc WHERE lpc.path_id = lp.id) as total_courses,
                       (SELECT COUNT(*) FROM learning_path_enrollments lpe WHERE lpe.path_id = lp.id) as total_students
                FROM learning_paths lp
                JOIN users u ON lp.created_by = u.id
                WHERE lp.is_published = 1 AND lp.deleted_at IS NULL
                ORDER BY lp.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả lộ trình (cho admin/teacher)
     */
    public function findAll(): array
    {
        $sql = "SELECT lp.*, u.username as creator_name,
                       (SELECT COUNT(*) FROM learning_path_courses lpc WHERE lpc.path_id = lp.id) as total_courses,
                       (SELECT COUNT(*) FROM learning_path_enrollments lpe WHERE lpe.path_id = lp.id) as total_students
                FROM learning_paths lp
                JOIN users u ON lp.created_by = u.id
                WHERE lp.deleted_at IS NULL
                ORDER BY lp.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm lộ trình theo ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT lp.*, u.username as creator_name
                FROM learning_paths lp
                JOIN users u ON lp.created_by = u.id
                WHERE lp.id = :id AND lp.deleted_at IS NULL
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Tìm lộ trình kèm danh sách khóa học
     */
    public function findByIdWithCourses(int $id): ?array
    {
        $path = $this->findById($id);
        if (!$path) {
            return null;
        }

        // Lấy danh sách khóa học trong lộ trình
        $path['courses'] = $this->getPathCourses($id);
        return $path;
    }

    /**
     * Tạo lộ trình mới
     */
    public function create(array $data): ?array
    {
        $sql = "INSERT INTO learning_paths (title, description, thumbnail, difficulty, estimated_hours, created_by, is_published)
                VALUES (:title, :description, :thumbnail, :difficulty, :estimated_hours, :created_by, :is_published)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'thumbnail' => $data['thumbnail'] ?? null,
            'difficulty' => $data['difficulty'] ?? 'beginner',
            'estimated_hours' => $data['estimated_hours'] ?? 0,
            'created_by' => $data['created_by'],
            'is_published' => $data['is_published'] ?? 0
        ]);

        if ($success) {
            return $this->findById($this->db->lastInsertId());
        }
        return null;
    }

    /**
     * Cập nhật lộ trình
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE learning_paths 
                SET title = :title, 
                    description = :description, 
                    thumbnail = COALESCE(:thumbnail, thumbnail),
                    difficulty = :difficulty, 
                    estimated_hours = :estimated_hours,
                    is_published = :is_published
                WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'thumbnail' => $data['thumbnail'] ?? null,
            'difficulty' => $data['difficulty'] ?? 'beginner',
            'estimated_hours' => $data['estimated_hours'] ?? 0,
            'is_published' => $data['is_published'] ?? 0
        ]);
    }

    /**
     * Soft delete lộ trình
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE learning_paths SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // ==========================================
    // LEARNING PATH - COURSES
    // ==========================================

    /**
     * Lấy danh sách khóa học trong lộ trình (kèm thông tin khóa học)
     */
    public function getPathCourses(int $pathId): array
    {
        $sql = "SELECT lpc.*, c.title, c.description, c.thumbnail, c.price, c.is_premium, 
                       c.level, c.status, c.total_lessons, c.estimated_duration,
                       u.username as teacher_name
                FROM learning_path_courses lpc
                JOIN courses c ON lpc.course_id = c.id
                JOIN users u ON c.teacher_id = u.id
                WHERE lpc.path_id = :path_id AND c.deleted_at IS NULL
                ORDER BY lpc.order_index ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['path_id' => $pathId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm khóa học vào lộ trình
     */
    public function addCourse(int $pathId, int $courseId, int $orderIndex = 0, bool $isRequired = true): bool
    {
        $sql = "INSERT INTO learning_path_courses (path_id, course_id, order_index, is_required)
                VALUES (:path_id, :course_id, :order_index, :is_required)
                ON DUPLICATE KEY UPDATE order_index = :order_index2, is_required = :is_required2";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'path_id' => $pathId,
            'course_id' => $courseId,
            'order_index' => $orderIndex,
            'is_required' => $isRequired ? 1 : 0,
            'order_index2' => $orderIndex,
            'is_required2' => $isRequired ? 1 : 0
        ]);
    }

    /**
     * Xóa khóa học khỏi lộ trình
     */
    public function removeCourse(int $pathId, int $courseId): bool
    {
        $sql = "DELETE FROM learning_path_courses WHERE path_id = :path_id AND course_id = :course_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['path_id' => $pathId, 'course_id' => $courseId]);
    }

    /**
     * Sắp xếp lại thứ tự khóa học trong lộ trình
     * @param array $courseOrders [['course_id' => 1, 'order_index' => 0], ...]
     */
    public function reorderCourses(int $pathId, array $courseOrders): bool
    {
        $sql = "UPDATE learning_path_courses SET order_index = :order_index 
                WHERE path_id = :path_id AND course_id = :course_id";
        $stmt = $this->db->prepare($sql);

        foreach ($courseOrders as $item) {
            $stmt->execute([
                'path_id' => $pathId,
                'course_id' => $item['course_id'],
                'order_index' => $item['order_index']
            ]);
        }
        return true;
    }

    // ==========================================
    // ENROLLMENT & PROGRESS
    // ==========================================

    /**
     * Đăng ký lộ trình cho user
     */
    public function enrollUser(int $userId, int $pathId): bool
    {
        // Check nếu đã enrolled
        if ($this->checkEnrollment($userId, $pathId)) {
            return false;
        }

        $sql = "INSERT INTO learning_path_enrollments (user_id, path_id, progress_percent) 
                VALUES (:user_id, :path_id, 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId, 'path_id' => $pathId]);
    }

    /**
     * Kiểm tra user đã enrolled lộ trình chưa
     */
    public function checkEnrollment(int $userId, int $pathId): bool
    {
        $sql = "SELECT id FROM learning_path_enrollments WHERE user_id = :user_id AND path_id = :path_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'path_id' => $pathId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Cập nhật tiến độ lộ trình
     */
    public function updateProgress(int $userId, int $pathId, int $percent): bool
    {
        $sql = "UPDATE learning_path_enrollments 
                SET progress_percent = :percent,
                    completed_at = CASE WHEN :percent2 >= 100 THEN CURRENT_TIMESTAMP ELSE NULL END
                WHERE user_id = :user_id AND path_id = :path_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'percent' => $percent,
            'percent2' => $percent,
            'user_id' => $userId,
            'path_id' => $pathId
        ]);
    }

    /**
     * Lấy tiến độ enrollment của user cho lộ trình
     */
    public function getUserEnrollment(int $userId, int $pathId): ?array
    {
        $sql = "SELECT * FROM learning_path_enrollments 
                WHERE user_id = :user_id AND path_id = :path_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'path_id' => $pathId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Lấy tất cả lộ trình đã enrolled của user
     */
    public function getUserEnrolledPaths(int $userId): array
    {
        $sql = "SELECT lpe.*, lp.title, lp.description, lp.thumbnail, lp.difficulty, lp.estimated_hours,
                       (SELECT COUNT(*) FROM learning_path_courses lpc WHERE lpc.path_id = lp.id) as total_courses
                FROM learning_path_enrollments lpe
                JOIN learning_paths lp ON lpe.path_id = lp.id
                WHERE lpe.user_id = :user_id AND lp.deleted_at IS NULL
                ORDER BY lpe.enrolled_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
