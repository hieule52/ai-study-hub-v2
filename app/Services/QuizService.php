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

    public function getQuizForStudent(int $lessonId): array
    {
        $quiz = $this->quizRepo->findQuizByLesson($lessonId);
        if (!$quiz) {
            throw new Exception("Không có bài tập cho lesson này.");
        }

        $questions = $this->quizRepo->findQuestionsByQuiz($quiz['id']);
        
        // Không gửi cờ is_correct cho Client/Student
        foreach ($questions as &$question) {
            $question['options'] = $this->quizRepo->findAnswersByQuestion($question['id'], false);
        }

        $quiz['questions'] = $questions;
        return $quiz;
    }

    public function submitAndCalculateScore(int $userId, int $quizId, array $studentAnswers): array
    {
        // studentAnswers format: [question_id => answer_id_luachon, ...]
        
        $questions = $this->quizRepo->findQuestionsByQuiz($quizId);
        if (empty($questions)) {
            throw new Exception("Lỗi: Bộ Quiz không tồn tại.");
        }

        $totalQuestions = count($questions);
        $correctCount = 0;
        $details = [];

        foreach ($questions as $q) {
            $qId = $q['id'];
            $options = $this->quizRepo->findAnswersByQuestion($qId, true);
            
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
}
