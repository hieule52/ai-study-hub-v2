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
     * Body: { message, base64_image?, lesson_id?, course_id? }
     */
    public function chat(Request $request, Response $response)
    {
        try {
            \App\Middlewares\RateLimitMiddleware::handle($request, $response, 20, 60, 'ai_chat');
            AuthMiddleware::handle($request, $response);

            $message = $request->input('message');
            $base64Image = $request->input('base64_image') ?? null;
            $lessonId = $request->input('lesson_id') ? (int)$request->input('lesson_id') : null;
            $courseId = $request->input('course_id') ? (int)$request->input('course_id') : null;
            $userId = $request->user->sub;

            $result = $this->aiService->chat($userId, $message, $base64Image, $lessonId, $courseId);
            
            $response->success("AI đã phân tích", $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
