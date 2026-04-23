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
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE lesson_id = :lesson_id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['lesson_id' => $lessonId]);
        $data = $stmt->fetch();
        return $data ? (array)$data : null;
    }

    public function findQuestionsByQuiz(int $quizId): array
    {
        $stmt = $this->db->prepare("SELECT id, question FROM questions WHERE quiz_id = :quiz_id AND deleted_at IS NULL");
        $stmt->execute(['quiz_id' => $quizId]);
        return $stmt->fetchAll();
    }

    public function findAnswersByQuestion(int $questionId, bool $includeCorrect = false): array
    {
        $sql = "SELECT id, answer_text";
        if ($includeCorrect) {
            $sql .= ", is_correct";
        }
        $sql .= " FROM answers WHERE question_id = :qid AND deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['qid' => $questionId]);
        return $stmt->fetchAll();
    }

    public function saveQuizResult(int $userId, int $quizId, int $score): bool
    {
        $stmt = $this->db->prepare("INSERT INTO quiz_results (user_id, quiz_id, score) VALUES (:uid, :qid, :score)");
        return $stmt->execute([
            'uid' => $userId,
            'qid' => $quizId,
            'score' => $score
        ]);
    }

    public function create(array $data): ?array
    {
        $stmt = $this->db->prepare("INSERT INTO quizzes (lesson_id, title) VALUES (:lesson_id, :title)");
        $success = $stmt->execute([
            'lesson_id' => $data['lesson_id'],
            'title' => $data['title']
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
            'id' => $id,
            'title' => $data['title']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE quizzes SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
