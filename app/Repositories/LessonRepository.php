<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class LessonRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findChaptersByCourse(int $courseId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM chapters WHERE course_id = :course_id AND deleted_at IS NULL ORDER BY order_index ASC");
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll();
    }

    public function findLessonsByChapter(int $chapterId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM lessons WHERE chapter_id = :chapter_id AND deleted_at IS NULL ORDER BY order_index ASC");
        $stmt->execute(['chapter_id' => $chapterId]);
        return $stmt->fetchAll();
    }

    public function findLessonById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM lessons WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? (array)$data : null;
    }

    public function markProgress(int $userId, int $lessonId): bool
    {
        // Kiểm tra xem đã có chưa
        $stmt = $this->db->prepare("SELECT id FROM lesson_progress WHERE user_id = :uid AND lesson_id = :lid");
        $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);
        
        if ($stmt->fetch()) {
            return true; // Đã đánh dấu hoàn thành từ trước
        }

        $insert = $this->db->prepare("INSERT INTO lesson_progress (user_id, lesson_id, is_completed, completed_at) VALUES (:uid, :lid, 1, CURRENT_TIMESTAMP)");
        return $insert->execute(['uid' => $userId, 'lid' => $lessonId]);
    }
}
