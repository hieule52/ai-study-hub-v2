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
     * Batch query: lấy tất cả lessons của nhiều chapters trong 1 query
     * Tránh N+1 problem trong getCourseCurriculum
     */
    public function findLessonsByChapterIds(array $chapterIds): array
    {
        if (empty($chapterIds)) return [];

        $placeholders = implode(',', array_fill(0, count($chapterIds), '?'));
        $stmt = $this->db->prepare("
            SELECT * FROM lessons
            WHERE chapter_id IN ({$placeholders}) AND deleted_at IS NULL
            ORDER BY chapter_id ASC, order_index ASC
        ");
        $stmt->execute(array_values($chapterIds));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    /**
     * Đánh dấu hoàn thành bài học
     * Dùng INSERT IGNORE (hoặc ON DUPLICATE KEY UPDATE) thay vì SELECT rồi INSERT
     * Requires UNIQUE KEY (user_id, lesson_id) — xem migration_v3.sql
     */
    public function markProgress(int $userId, int $lessonId): bool
    {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO lesson_progress (user_id, lesson_id, is_completed, completed_at)
            VALUES (:uid, :lid, 1, CURRENT_TIMESTAMP)
        ");
        $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);
        return true; // INSERT IGNORE không throw exception nếu đã tồn tại
    }

    public function create(array $data): ?array
    {
        $sql = "INSERT INTO lessons (
                    chapter_id, title, content_type, lesson_type, video_url, 
                    video_filename, video_path, video_thumbnail, video_size, 
                    duration, video_duration, content, objectives, ai_summary, 
                    video_transcript, lesson_context, key_topics, lesson_keywords, 
                    order_index, is_free, secure_token, storage_driver
                ) 
                VALUES (
                    :chapter_id, :title, :content_type, :lesson_type, :video_url, 
                    :video_filename, :video_path, :video_thumbnail, :video_size, 
                    :duration, :video_duration, :content, :objectives, :ai_summary, 
                    :video_transcript, :lesson_context, :key_topics, :lesson_keywords, 
                    :order_index, :is_free, :secure_token, :storage_driver
                )";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'chapter_id' => $data['chapter_id'],
            'title' => $data['title'],
            'content_type' => $data['content_type'] ?? 'video',
            'lesson_type' => $data['lesson_type'] ?? ($data['content_type'] ?? 'video'),
            'video_url' => $data['video_url'] ?? '',
            'video_filename' => $data['video_filename'] ?? null,
            'video_path' => $data['video_path'] ?? null,
            'video_thumbnail' => $data['video_thumbnail'] ?? null,
            'video_size' => $data['video_size'] ?? 0,
            'duration' => $data['duration'] ?? 0,
            'video_duration' => $data['video_duration'] ?? ($data['duration'] ?? 0),
            'content' => $data['content'] ?? '',
            'objectives' => $data['objectives'] ?? null,
            'ai_summary' => $data['ai_summary'] ?? null,
            'video_transcript' => $data['video_transcript'] ?? null,
            'lesson_context' => $data['lesson_context'] ?? null,
            'key_topics' => $data['key_topics'] ?? null,
            'lesson_keywords' => $data['lesson_keywords'] ?? ($data['key_topics'] ?? null),
            'order_index' => $data['order_index'] ?? 0,
            'is_free' => $data['is_free'] ?? 0,
            'secure_token' => $data['secure_token'] ?? null,
            'storage_driver' => $data['storage_driver'] ?? 'local'
        ]);

        if ($success) {
            return $this->findLessonById($this->db->lastInsertId());
        }
        return null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE lessons SET 
                    title = :title, 
                    content_type = :content_type, 
                    lesson_type = :lesson_type,
                    video_url = :video_url, 
                    video_filename = COALESCE(:video_filename, video_filename),
                    video_path = COALESCE(:video_path, video_path),
                    video_thumbnail = COALESCE(:video_thumbnail, video_thumbnail),
                    video_size = CASE WHEN :video_size > 0 THEN :video_size2 ELSE video_size END,
                    duration = CASE WHEN :duration > 0 THEN :duration2 ELSE duration END,
                    video_duration = CASE WHEN :video_duration > 0 THEN :video_duration2 ELSE video_duration END,
                    content = :content, 
                    objectives = :objectives, 
                    ai_summary = :ai_summary,
                    video_transcript = :video_transcript, 
                    lesson_context = :lesson_context, 
                    key_topics = :key_topics,
                    lesson_keywords = :lesson_keywords,
                    order_index = :order_index, 
                    is_free = :is_free,
                    secure_token = COALESCE(:secure_token, secure_token),
                    storage_driver = :storage_driver
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'content_type' => $data['content_type'] ?? 'video',
            'lesson_type' => $data['lesson_type'] ?? ($data['content_type'] ?? 'video'),
            'video_url' => $data['video_url'] ?? '',
            'video_filename' => $data['video_filename'] ?? null,
            'video_path' => $data['video_path'] ?? null,
            'video_thumbnail' => $data['video_thumbnail'] ?? null,
            'video_size' => $data['video_size'] ?? 0,
            'video_size2' => $data['video_size'] ?? 0,
            'duration' => $data['duration'] ?? 0,
            'duration2' => $data['duration'] ?? 0,
            'video_duration' => $data['video_duration'] ?? ($data['duration'] ?? 0),
            'video_duration2' => $data['video_duration'] ?? ($data['duration'] ?? 0),
            'content' => $data['content'] ?? '',
            'objectives' => $data['objectives'] ?? null,
            'ai_summary' => $data['ai_summary'] ?? null,
            'video_transcript' => $data['video_transcript'] ?? null,
            'lesson_context' => $data['lesson_context'] ?? null,
            'key_topics' => $data['key_topics'] ?? null,
            'lesson_keywords' => $data['lesson_keywords'] ?? ($data['key_topics'] ?? null),
            'order_index' => $data['order_index'] ?? 0,
            'is_free' => $data['is_free'] ?? 0,
            'secure_token' => $data['secure_token'] ?? null,
            'storage_driver' => $data['storage_driver'] ?? 'local'
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
