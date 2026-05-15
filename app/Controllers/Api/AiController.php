<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\AiService;
use App\Middlewares\AuthMiddleware;
use Exception;

class AiController
{
    private AiService $aiService;

    public function __construct()
    {
        $this->aiService = new AiService();
    }

    /**
     * POST /api/ai/chat
     * Body: { message, lesson_id?, course_id? }
     */
    public function chat(Request $request, Response $response)
    {
        try {
            \App\Middlewares\RateLimitMiddleware::handle($request, $response, 20, 60, 'ai_chat');
            AuthMiddleware::handle($request, $response);

            $message = $request->input('message');
            $lessonId = $request->input('lesson_id') ? (int)$request->input('lesson_id') : null;
            $courseId = $request->input('course_id') ? (int)$request->input('course_id') : null;
            $userId = $request->user->sub;

            $result = $this->aiService->chat($userId, $message, null, $lessonId, $courseId);
            
            $response->success("AI đã phản hồi", $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/ai/history
     * Body: { lesson_id }
     */
    public function clearHistory(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $lessonId = $request->input('lesson_id');
            $userId = $request->user->sub;

            if (!$lessonId) throw new Exception("Thiếu ID bài học.");

            $repo = new \App\Repositories\AiRepository();
            $repo->clearHistory($userId, (int)$lessonId);

            $response->success("Đã xóa lịch sử hội thoại.");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
