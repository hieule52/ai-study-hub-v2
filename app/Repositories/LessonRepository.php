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

    /**
     * Tìm bài học kèm trạng thái hoàn thành của user
     */
    public function findLessonWithProgress(int $lessonId, int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT l.*, 
                   CASE WHEN lp.is_completed = 1 THEN 1 ELSE 0 END as is_completed,
                   lp.completed_at
            FROM lessons l
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = :user_id
            WHERE l.id = :lesson_id AND l.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['lesson_id' => $lessonId, 'user_id' => $userId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ?: null;
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

    public function create(array $data): ?array
    {
        $sql = "INSERT INTO lessons (chapter_id, title, content_type, video_url, video_filename, video_size, duration, content, ai_summary, order_index, is_free) 
                VALUES (:chapter_id, :title, :content_type, :video_url, :video_filename, :video_size, :duration, :content, :ai_summary, :order_index, :is_free)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'chapter_id' => $data['chapter_id'],
            'title' => $data['title'],
            'content_type' => $data['content_type'] ?? 'video',
            'video_url' => $data['video_url'] ?? '',
            'video_filename' => $data['video_filename'] ?? null,
            'video_size' => $data['video_size'] ?? 0,
            'duration' => $data['duration'] ?? 0,
            'content' => $data['content'] ?? '',
            'ai_summary' => $data['ai_summary'] ?? null,
            'order_index' => $data['order_index'] ?? 0,
            'is_free' => $data['is_free'] ?? 0
        ]);

        if ($success) {
            return $this->findLessonById($this->db->lastInsertId());
        }
        return null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE lessons SET title = :title, content_type = :content_type, 
                video_url = :video_url, video_filename = COALESCE(:video_filename, video_filename),
                video_size = CASE WHEN :video_size > 0 THEN :video_size2 ELSE video_size END,
                duration = CASE WHEN :duration > 0 THEN :duration2 ELSE duration END,
                content = :content, ai_summary = :ai_summary, order_index = :order_index, is_free = :is_free
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'content_type' => $data['content_type'] ?? 'video',
            'video_url' => $data['video_url'] ?? '',
            'video_filename' => $data['video_filename'] ?? null,
            'video_size' => $data['video_size'] ?? 0,
            'video_size2' => $data['video_size'] ?? 0,
            'duration' => $data['duration'] ?? 0,
            'duration2' => $data['duration'] ?? 0,
            'content' => $data['content'] ?? '',
            'ai_summary' => $data['ai_summary'] ?? null,
            'order_index' => $data['order_index'] ?? 0,
            'is_free' => $data['is_free'] ?? 0
        ]);
    }

    /**
     * Sắp xếp lại thứ tự bài học
     */
    public function reorder(int $chapterId, array $lessonOrders): bool
    {
        $sql = "UPDATE lessons SET order_index = :order_index WHERE id = :id AND chapter_id = :chapter_id";
        $stmt = $this->db->prepare($sql);

        foreach ($lessonOrders as $item) {
            $stmt->execute([
                'id' => $item['id'],
                'order_index' => $item['order_index'],
                'chapter_id' => $chapterId
            ]);
        }
        return true;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE lessons SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
