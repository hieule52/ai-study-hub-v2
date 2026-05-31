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
     * Tìm bài học kèm trạng thái hoàn thành và tiến độ của user
     */
    public function findLessonWithProgress(int $lessonId, int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT l.*, 
                   CASE WHEN lp.is_completed = 1 THEN 1 ELSE 0 END as is_completed,
                   lp.completed_at,
                   COALESCE(lp.video_progress, 0) as video_progress,
                   COALESCE(lp.text_progress, 0) as text_progress
            FROM lessons l
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = :user_id
            WHERE l.id = :lesson_id AND l.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['lesson_id' => $lessonId, 'user_id' => $userId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Lấy toàn bộ tiến độ bài học của user trong một khóa học (tránh N+1)
     * Trả về mảng [lesson_id => ['is_completed', 'video_progress', 'text_progress']]
     */
    public function findProgressByCourse(int $userId, int $courseId): array
    {
        $stmt = $this->db->prepare("
            SELECT lp.lesson_id,
                   COALESCE(lp.is_completed, 0)     as is_completed,
                   COALESCE(lp.video_progress, 0)   as video_progress,
                   COALESCE(lp.text_progress, 0)    as text_progress,
                   lp.completed_at
            FROM lesson_progress lp
            JOIN lessons l      ON lp.lesson_id = l.id
            JOIN chapters c     ON l.chapter_id = c.id
            WHERE lp.user_id = :uid
              AND c.course_id = :cid
              AND l.deleted_at IS NULL
        ");
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $map = [];
        foreach ($rows as $row) {
            $map[(int)$row['lesson_id']] = $row;
        }
        return $map;
    }

    public function findLessonById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM lessons WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? (array)$data : null;
    }

    /**
     * Đánh dấu hoàn thành bài học (legacy — vẫn giữ để tương thích)
     */
    public function markProgress(int $userId, int $lessonId): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO lesson_progress (user_id, lesson_id, is_completed, completed_at, video_progress, text_progress)
            VALUES (:uid, :lid, 1, CURRENT_TIMESTAMP, 100, 100)
            ON DUPLICATE KEY UPDATE
                is_completed = 1,
                completed_at = COALESCE(completed_at, CURRENT_TIMESTAMP),
                video_progress = GREATEST(video_progress, 100),
                text_progress  = GREATEST(text_progress, 100)
        ");
        $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);
        return true;
    }

    /**
     * Cập nhật các cột tiến độ video/text trong DB
     */
    public function updateLessonProgressValues(int $userId, int $lessonId, int $videoProgress, int $textProgress): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO lesson_progress
                (user_id, lesson_id, video_progress, text_progress, is_completed)
            VALUES
                (:uid, :lid, :vp, :tp, 0)
            ON DUPLICATE KEY UPDATE
                video_progress = GREATEST(video_progress, :vp2),
                text_progress  = GREATEST(text_progress,  :tp2)
        ");
        $stmt->execute([
            'uid' => $userId,
            'lid' => $lessonId,
            'vp'  => $videoProgress,
            'tp'  => $textProgress,
            'vp2' => $videoProgress,
            'tp2' => $textProgress,
        ]);
    }

    /**
     * Đặt trạng thái hoàn thành bài học
     */
    public function setLessonCompleted(int $userId, int $lessonId, bool $isCompleted): void
    {
        $completed = $isCompleted ? 1 : 0;
        $stmt = $this->db->prepare("
            UPDATE lesson_progress
            SET is_completed = :completed,
                completed_at = CASE WHEN :completed2 = 1 THEN COALESCE(completed_at, CURRENT_TIMESTAMP) ELSE NULL END
            WHERE user_id = :uid AND lesson_id = :lid
        ");
        $stmt->execute([
            'completed'  => $completed,
            'completed2' => $completed,
            'uid'        => $userId,
            'lid'        => $lessonId,
        ]);
    }

    /**
     * Đánh giá xem bài học đã hoàn thành theo công thức LMS hay chưa:
     * Video >= 80% (nếu có) AND Text >= 80% (nếu có) AND Quiz Passed (nếu có)
     */
    public function evaluateLessonCompletion(int $userId, int $lessonId, int $currentVideoProgress = 0, int $currentTextProgress = 0): bool
    {
        // 1. Lấy thông tin bài học
        $stmt = $this->db->prepare("SELECT content_type, content, video_url, video_path FROM lessons WHERE id = :lid LIMIT 1");
        $stmt->execute(['lid' => $lessonId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$lesson) return false;

        // Lấy tiến độ đã lưu hiện tại trong DB
        $stmtProg = $this->db->prepare("SELECT video_progress, text_progress FROM lesson_progress WHERE user_id = :uid AND lesson_id = :lid LIMIT 1");
        $stmtProg->execute(['uid' => $userId, 'lid' => $lessonId]);
        $prog = $stmtProg->fetch(PDO::FETCH_ASSOC);

        $savedVideo = $prog ? (int)$prog['video_progress'] : 0;
        $savedText  = $prog ? (int)$prog['text_progress'] : 0;

        $videoProgress = max($savedVideo, $currentVideoProgress);
        $textProgress  = max($savedText, $currentTextProgress);

        // Quy tắc 1: Nếu bài học có video, tiến độ xem video phải đạt tối thiểu 80%
        $hasVideo = ($lesson['content_type'] === 'video' || !empty($lesson['video_url']) || !empty($lesson['video_path']));
        if ($hasVideo && $videoProgress < 80) {
            return false;
        }

        // Quy tắc 2: Nếu bài học có tài liệu đọc (text), tiến độ cuộn đọc phải đạt tối thiểu 80%
        $hasText = ($lesson['content_type'] === 'text' || (!empty($lesson['content']) && strlen(trim(strip_tags($lesson['content']))) > 50));
        if ($hasText && $textProgress < 80) {
            return false;
        }

        // Quy tắc 3: Nếu bài học có Quiz, học viên bắt buộc phải thi đỗ (score >= passing_score)
        $stmtQuiz = $this->db->prepare("SELECT id, COALESCE(passing_score, 80) as passing_score FROM quizzes WHERE lesson_id = :lid AND deleted_at IS NULL LIMIT 1");
        $stmtQuiz->execute(['lid' => $lessonId]);
        $quiz = $stmtQuiz->fetch(PDO::FETCH_ASSOC);
        if ($quiz) {
            $stmtScore = $this->db->prepare("SELECT MAX(score) FROM quiz_results WHERE user_id = :uid AND quiz_id = :qid");
            $stmtScore->execute(['uid' => $userId, 'qid' => $quiz['id']]);
            $maxScore = $stmtScore->fetchColumn();
            if ($maxScore === null || (int)$maxScore < (int)$quiz['passing_score']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Cập nhật tiến độ video/text của bài học.
     * Tự động đánh dấu completed theo công thức LMS đầy đủ.
     */
    public function updateLessonProgress(int $userId, int $lessonId, int $videoProgress, int $textProgress): array
    {
        // 1. Lưu các tiến độ số vào DB
        $this->updateLessonProgressValues($userId, $lessonId, $videoProgress, $textProgress);

        // 2. Đánh giá độ hoàn thành theo công thức tích hợp
        $isCompleted = $this->evaluateLessonCompletion($userId, $lessonId, $videoProgress, $textProgress);

        // 3. Cập nhật cờ is_completed
        $this->setLessonCompleted($userId, $lessonId, $isCompleted);

        // 4. Lấy trạng thái mới nhất từ DB
        $stmt2 = $this->db->prepare("
            SELECT is_completed, video_progress, text_progress, completed_at
            FROM lesson_progress WHERE user_id = :uid AND lesson_id = :lid LIMIT 1
        ");
        $stmt2->execute(['uid' => $userId, 'lid' => $lessonId]);
        $row = $stmt2->fetch(\PDO::FETCH_ASSOC);
        return $row ?: ['is_completed' => ($isCompleted ? 1 : 0), 'video_progress' => $videoProgress, 'text_progress' => $textProgress];
    }

    public function create(array $data): ?array
    {
        $sql = "INSERT INTO lessons (
                    chapter_id, title, content_type, lesson_type, video_url, 
                    video_filename, video_path, video_thumbnail, video_size, 
                    duration, video_duration, content, objectives, ai_summary, 
                    video_transcript, lesson_context, key_topics, lesson_keywords, 
                    order_index, is_free, secure_token, storage_driver,
                    transcript_status, strict_ai_mode, teacher_notes,
                    enable_auto_summary, enable_auto_keywords, enable_auto_context
                ) 
                VALUES (
                    :chapter_id, :title, :content_type, :lesson_type, :video_url, 
                    :video_filename, :video_path, :video_thumbnail, :video_size, 
                    :duration, :video_duration, :content, :objectives, :ai_summary, 
                    :video_transcript, :lesson_context, :key_topics, :lesson_keywords, 
                    :order_index, :is_free, :secure_token, :storage_driver,
                    :transcript_status, :strict_ai_mode, :teacher_notes,
                    :enable_auto_summary, :enable_auto_keywords, :enable_auto_context
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
            'storage_driver' => $data['storage_driver'] ?? 'local',
            'transcript_status' => $data['transcript_status'] ?? 'empty',
            'strict_ai_mode' => $data['strict_ai_mode'] ?? 0,
            'teacher_notes' => $data['teacher_notes'] ?? null,
            'enable_auto_summary' => $data['enable_auto_summary'] ?? 1,
            'enable_auto_keywords' => $data['enable_auto_keywords'] ?? 1,
            'enable_auto_context' => $data['enable_auto_context'] ?? 1
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
                    storage_driver = :storage_driver,
                    strict_ai_mode = :strict_ai_mode,
                    teacher_notes = :teacher_notes,
                    enable_auto_summary = :enable_auto_summary,
                    enable_auto_keywords = :enable_auto_keywords,
                    enable_auto_context = :enable_auto_context
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
            'storage_driver' => $data['storage_driver'] ?? 'local',
            'strict_ai_mode' => $data['strict_ai_mode'] ?? 0,
            'teacher_notes' => $data['teacher_notes'] ?? null,
            'enable_auto_summary' => $data['enable_auto_summary'] ?? 1,
            'enable_auto_keywords' => $data['enable_auto_keywords'] ?? 1,
            'enable_auto_context' => $data['enable_auto_context'] ?? 1
        ]);
    }

    /**
     * Xác minh bản dịch — cập nhật transcript_status, verified_by_teacher, verified_at
     */
    public function verifyTranscript(int $lessonId, int $teacherId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE lessons SET 
                transcript_status = 'teacher_verified',
                verified_by_teacher = :teacher_id,
                verified_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $lessonId, 'teacher_id' => $teacherId]);
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
