<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\QuizService;
use App\Middlewares\AuthMiddleware;
use Exception;

class QuizController
{
    private QuizService $quizService;

    public function __construct()
    {
        $this->quizService = new QuizService();
    }

    public function showByLesson(Request $request, Response $response, string $lessonId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $quiz = $this->quizService->getQuizForStudent((int)$lessonId);
            $response->success("Tải bộ câu hỏi thành công", $quiz);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    public function submit(Request $request, Response $response, string $quizId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            
            $userId = $request->user->sub; // Get from JWT
            $studentAnswers = $request->input('answers', []); // ['12' => 45, '13' => 48] array question_id => answer_id
            
            $result = $this->quizService->submitAndCalculateScore($userId, (int)$quizId, (array)$studentAnswers);

            $response->success("Nộp bài thi thành công", $result, 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * GET /api/quizzes/:id/history
     * Lịch sử làm bài của học viên
     */
    public function history(Request $request, Response $response, string $quizId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = (int)$request->user->sub;

            $history = $this->quizService->getHistory($userId, (int)$quizId);
            $response->success("Lịch sử làm bài", $history);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}

