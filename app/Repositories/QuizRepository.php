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
}
