<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ChapterRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(array $data): ?array
    {
        $sql = "INSERT INTO chapters (course_id, title, order_index) VALUES (:course_id, :title, :order_index)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'course_id' => $data['course_id'],
            'title' => $data['title'],
            'order_index' => $data['order_index'] ?? 0
        ]);

        if ($success) {
            return $this->findById($this->db->lastInsertId());
        }
        return null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM chapters WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $data : null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE chapters SET title = :title, order_index = :order_index WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'order_index' => $data['order_index'] ?? 0
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE chapters SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Sắp xếp lại thứ tự chương
     * @param array $chapterOrders [['id' => 1, 'order_index' => 0], ...]
     */
    public function reorder(int $courseId, array $chapterOrders): bool
    {
        $sql = "UPDATE chapters SET order_index = :order_index WHERE id = :id AND course_id = :course_id";
        $stmt = $this->db->prepare($sql);

        foreach ($chapterOrders as $item) {
            $stmt->execute([
                'id' => $item['id'],
                'order_index' => $item['order_index'],
                'course_id' => $courseId
            ]);
        }
        return true;
    }

    /**
     * Đếm số bài học trong chương
     */
    public function countLessons(int $chapterId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM lessons WHERE chapter_id = :cid AND deleted_at IS NULL");
        $stmt->execute(['cid' => $chapterId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Tìm tất cả chương theo course (kèm số bài)
     */
    public function findByCourseWithCount(int $courseId): array
    {
        $sql = "SELECT ch.*, 
                       (SELECT COUNT(*) FROM lessons l WHERE l.chapter_id = ch.id AND l.deleted_at IS NULL) as lesson_count
                FROM chapters ch
                WHERE ch.course_id = :course_id AND ch.deleted_at IS NULL
                ORDER BY ch.order_index ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
