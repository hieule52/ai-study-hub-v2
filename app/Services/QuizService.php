<?php

namespace App\Services;

use App\Repositories\QuizRepository;
use Exception;

class QuizService
{
    private QuizRepository $quizRepo;

    public function __construct()
    {
        $this->quizRepo = new QuizRepository();
    }

    public function getQuizForStudent(int $lessonId, string $userRole = 'student'): array
    {
        $quiz = $this->quizRepo->findQuizByLesson($lessonId);
        if (!$quiz) {
            throw new Exception("Không có bài tập cho lesson này.");
        }

        // Fetch course status
        $db = \App\Core\Database::connect();
        $stmtStatus = $db->prepare("
            SELECT c.status, c.id as course_id 
            FROM courses c
            JOIN chapters ch ON c.id = ch.course_id
            JOIN lessons l ON ch.id = l.chapter_id
            WHERE l.id = ?
        ");
        $stmtStatus->execute([$lessonId]);
        $courseInfo = $stmtStatus->fetch(\PDO::FETCH_ASSOC);

        if ($courseInfo && $courseInfo['status'] === 'pending_reapproval' && $userRole !== 'teacher' && $userRole !== 'admin') {
            // Find oldest update log for this quiz
            $stmtLog = $db->prepare("
                SELECT * FROM course_change_logs 
                WHERE course_id = ? AND entity_type = 'quiz' AND entity_id = ? AND action_type = 'update'
                ORDER BY id ASC LIMIT 1
            ");
            $stmtLog->execute([$courseInfo['course_id'], (int)$quiz['id']]);
            $log = $stmtLog->fetch(\PDO::FETCH_ASSOC);
            if ($log && $log['old_snapshot']) {
                $oldQuiz = json_decode($log['old_snapshot'], true);
                
                // Format the questions for student view (remove is_correct from choices)
                $questions = $oldQuiz['questions'] ?? [];
                foreach ($questions as &$question) {
                    $options = [];
                    foreach (($question['answers'] ?? []) as $ans) {
                        $options[] = [
                            'id' => $ans['id'],
                            'answer_text' => $ans['answer_text'] ?? $ans['text'] ?? ''
                        ];
                    }
                    $question['options'] = $options;
                    unset($question['answers']);
                }
                
                $oldQuiz['questions'] = $questions;
                return $oldQuiz;
            }
        }

        $questions = $this->quizRepo->findQuestionsByQuiz($quiz['id']);
        
        // Không gửi cờ is_correct cho Client/Student
        foreach ($questions as &$question) {
            $question['options'] = $this->quizRepo->findAnswersByQuestion($question['id'], false);
        }

        $quiz['questions'] = $questions;
        return $quiz;
    }

    public function submitAndCalculateScore(int $userId, int $quizId, array $studentAnswers, string $userRole = 'student'): array
    {
        // studentAnswers format: [question_id => answer_id_luachon, ...]
        
        // Fetch course status
        $db = \App\Core\Database::connect();
        $stmtStatus = $db->prepare("
            SELECT c.status, c.id as course_id 
            FROM courses c
            JOIN chapters ch ON c.id = ch.course_id
            JOIN lessons l ON ch.id = l.chapter_id
            JOIN quizzes q ON l.id = q.lesson_id
            WHERE q.id = ?
        ");
        $stmtStatus->execute([$quizId]);
        $courseInfo = $stmtStatus->fetch(\PDO::FETCH_ASSOC);

        $useSnapshot = false;
        $questions = [];

        if ($courseInfo && $courseInfo['status'] === 'pending_reapproval' && $userRole !== 'teacher' && $userRole !== 'admin') {
            // Find oldest change log for this quiz
            $stmtLog = $db->prepare("
                SELECT * FROM course_change_logs 
                WHERE course_id = ? AND entity_type = 'quiz' AND entity_id = ? AND action_type = 'update'
                ORDER BY id ASC LIMIT 1
            ");
            $stmtLog->execute([$courseInfo['course_id'], $quizId]);
            $log = $stmtLog->fetch(\PDO::FETCH_ASSOC);
            if ($log && $log['old_snapshot']) {
                $oldQuiz = json_decode($log['old_snapshot'], true);
                $questions = $oldQuiz['questions'] ?? [];
                $useSnapshot = true;
            }
        }

        if (!$useSnapshot) {
            $questions = $this->quizRepo->findQuestionsByQuiz($quizId);
        }

        if (empty($questions)) {
            throw new Exception("Lỗi: Bộ Quiz không tồn tại.");
        }

        $totalQuestions = count($questions);
        $correctCount = 0;
        $details = [];

        foreach ($questions as $q) {
            $qId = $q['id'];
            
            if ($useSnapshot) {
                // If using snapshot, answers are already inside the question object
                $options = $q['answers'] ?? [];
            } else {
                $options = $this->quizRepo->findAnswersByQuestion($qId, true);
            }
            
            $correctAnswerId = null;
            foreach ($options as $opt) {
                if ($opt['is_correct'] == 1) {
                    $correctAnswerId = $opt['id'];
                    break;
                }
            }

            $userChoice = $studentAnswers[$qId] ?? null;
            $isCorrectParam = ($userChoice == $correctAnswerId);

            if ($isCorrectParam) {
                $correctCount++;
            }

            $details[] = [
                'question_id' => $qId,
                'is_correct' => $isCorrectParam,
                'correct_answer_id' => $correctAnswerId,
                'user_choice' => $userChoice
            ];
        }

        // Tỷ lệ %
        $score = (int) round(($correctCount / $totalQuestions) * 100);

        // Lưu vào schema
        $this->quizRepo->saveQuizResult($userId, $quizId, $score);

        return [
            'score' => $score,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'details' => $details
        ];
    }

    /**
     * Lịch sử làm bài của một học viên theo quizId
     */
    public function getHistory(int $userId, int $quizId): array
    {
        return $this->quizRepo->getResultsByUserAndQuiz($userId, $quizId);
    }
}

