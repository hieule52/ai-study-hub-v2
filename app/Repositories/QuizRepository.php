<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class QuizRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findQuizByLesson(int $lessonId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE lesson_id = :lesson_id LIMIT 1");
        $stmt->execute(['lesson_id' => $lessonId]);
        $data = $stmt->fetch();
        return $data ? (array)$data : null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? (array)$data : null;
    }

    public function findQuestionsByQuiz(int $quizId): array
    {
        $stmt = $this->db->prepare("SELECT id, question FROM questions WHERE quiz_id = :quiz_id ORDER BY id ASC");
        $stmt->execute(['quiz_id' => $quizId]);
        return $stmt->fetchAll();
    }

    public function findAnswersByQuestion(int $questionId, bool $includeCorrect = false): array
    {
        $sql = "SELECT id, answer_text";
        if ($includeCorrect) {
            $sql .= ", is_correct";
        }
        $sql .= " FROM answers WHERE question_id = :qid ORDER BY id ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['qid' => $questionId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy toàn bộ quiz kèm câu hỏi và đáp án (để teacher chỉnh sửa)
     */
    public function getFullQuiz(int $lessonId): ?array
    {
        $quiz = $this->findQuizByLesson($lessonId);
        if (!$quiz) return null;

        $questions = $this->findQuestionsByQuiz((int)$quiz['id']);
        foreach ($questions as &$q) {
            $q['answers'] = $this->findAnswersByQuestion((int)$q['id'], true);
        }
        $quiz['questions'] = $questions;
        return $quiz;
    }

    /**
     * Lưu toàn bộ quiz + câu hỏi + đáp án (tạo mới hoặc thay thế)
     * Xóa quiz cũ của lesson rồi tạo lại hoàn toàn.
     */
    public function saveFullQuiz(int $lessonId, string $title, array $questions): array
    {
        $this->db->beginTransaction();
        try {
            // Soft-delete quiz cũ nếu có
            $this->db->prepare("UPDATE quizzes SET deleted_at = CURRENT_TIMESTAMP WHERE lesson_id = :lid")
                     ->execute(['lid' => $lessonId]);

            // Tạo quiz mới
            $stmt = $this->db->prepare("INSERT INTO quizzes (lesson_id, title) VALUES (:lid, :title)");
            $stmt->execute(['lid' => $lessonId, 'title' => $title]);
            $quizId = (int)$this->db->lastInsertId();

            foreach ($questions as $q) {
                if (empty(trim($q['question'] ?? ''))) continue;

                $stmt2 = $this->db->prepare("INSERT INTO questions (quiz_id, question) VALUES (:qid, :q)");
                $stmt2->execute(['qid' => $quizId, 'q' => trim($q['question'])]);
                $questionId = (int)$this->db->lastInsertId();

                foreach (($q['answers'] ?? []) as $a) {
                    if (empty(trim($a['text'] ?? ''))) continue;
                    $stmt3 = $this->db->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (:qid, :txt, :ok)");
                    $stmt3->execute([
                        'qid' => $questionId,
                        'txt' => trim($a['text']),
                        'ok'  => isset($a['is_correct']) && $a['is_correct'] ? 1 : 0
                    ]);
                }
            }

            $this->db->commit();
            return ['quiz_id' => $quizId, 'question_count' => count($questions)];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Lưu kết quả quiz
     * DB có UNIQUE(user_id, quiz_id) — chỉ lưu 1 kết quả mới nhất per user per quiz
     * Dùng ON DUPLICATE KEY UPDATE để không throw error khi submit lại
     */
    public function saveQuizResult(int $userId, int $quizId, int $score): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO quiz_results (user_id, quiz_id, score)
            VALUES (:uid, :qid, :score)
            ON DUPLICATE KEY UPDATE score = :score2, created_at = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([
            'uid'    => $userId,
            'qid'    => $quizId,
            'score'  => $score,
            'score2' => $score,
        ]);
    }

    public function create(array $data): ?array
    {
        $stmt = $this->db->prepare("INSERT INTO quizzes (lesson_id, title) VALUES (:lesson_id, :title)");
        $success = $stmt->execute([
            'lesson_id' => $data['lesson_id'],
            'title'     => $data['title']
        ]);
        
        if ($success) {
            $stmt2 = $this->db->prepare("SELECT * FROM quizzes WHERE id = :id");
            $stmt2->execute(['id' => $this->db->lastInsertId()]);
            return $stmt2->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE quizzes SET title = :title WHERE id = :id");
        return $stmt->execute([
            'id'    => $id,
            'title' => $data['title']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE quizzes SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lịch sử làm bài của học viên theo quiz
     * DB chỉ có cột: id, user_id, quiz_id, score, created_at
     * UNIQUE(user_id, quiz_id) — chỉ có 1 row per user per quiz
     */
    public function getResultsByUserAndQuiz(int $userId, int $quizId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, score, created_at
            FROM quiz_results
            WHERE user_id = :uid AND quiz_id = :qid
            LIMIT 1
        ");
        $stmt->execute(['uid' => $userId, 'qid' => $quizId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? [$row] : []; // wrap in array for API consistency
    }
}

