<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\AiService;
use App\Services\LessonGuardService;
use App\Middlewares\AuthMiddleware;
use Exception;

class AiController
{
    private AiService $aiService;
    private LessonGuardService $guardService;

    public function __construct()
    {
        $this->aiService    = new AiService();
        $this->guardService = new LessonGuardService();
    }

    /**
     * POST /api/ai/chat
     * AI số 1: Gia sư AI tổng quát — Guest/Student/Teacher
     * Body: { message, course_id? }
     *
     * Guest được phép dùng (không cần JWT) nhưng sẽ không lưu lịch sử.
     */
    public function chat(Request $request, Response $response)
    {
        try {
            \App\Middlewares\RateLimitMiddleware::handle($request, $response, 20, 60, 'ai_assistant');

            // Optional auth (Guest cũng dùng được)
            $userId   = 0;
            $userRole = 'guest';
            $authHeader = $request->getHeader('Authorization');
            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $payload = \App\Core\JWTHandler::decode($matches[1]);
                if ($payload) {
                    $userId   = (int)($payload->sub  ?? 0);
                    $userRole = $payload->role ?? 'guest';
                }
            }

            $message  = trim($request->input('message') ?? '');
            $courseId = $request->input('course_id') ? (int)$request->input('course_id') : null;

            if (empty($message)) {
                $response->error("Vui lòng nhập nội dung câu hỏi.", 400);
                return;
            }

            $result = $this->aiService->chatAssistant($userId, $message, $userRole, $courseId);
            $response->success("AI đã phản hồi", $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * POST /api/ai/tutor
     * AI số 2: AI Tutor nghiêm ngặt — chỉ Student, trong trang học bài
     * Body: { message, lesson_id, course_id? }
     */
    public function tutor(Request $request, Response $response)
    {
        try {
            \App\Middlewares\RateLimitMiddleware::handle($request, $response, 30, 60, 'ai_tutor');
            AuthMiddleware::handle($request, $response);

            $userRole = $request->user->role ?? 'guest';
            if ($userRole !== 'student') {
                $response->error("AI Tutor chỉ dành cho học viên.", 403);
                return;
            }

            $userId   = (int)$request->user->sub;
            $message  = trim($request->input('message') ?? '');
            $lessonId = $request->input('lesson_id') ? (int)$request->input('lesson_id') : null;
            $courseId = $request->input('course_id') ? (int)$request->input('course_id') : null;

            if (empty($message)) {
                $response->error("Vui lòng nhập nội dung câu hỏi.", 400);
                return;
            }
            if (!$lessonId) {
                $response->error("Thiếu thông tin bài học (lesson_id).", 400);
                return;
            }

            // Sequential lock guard — AI Tutor không được trả lời bài học chưa mở khoá
            if (!$this->guardService->canAccessLesson($userId, $lessonId)) {
                $db = \App\Core\Database::connect();
                $s  = $db->prepare("SELECT c.course_id FROM chapters c JOIN lessons l ON l.chapter_id = c.id WHERE l.id = ? LIMIT 1");
                $s->execute([$lessonId]);
                $courseId2  = (int)$s->fetchColumn();
                $redirectId = $courseId2 ? $this->guardService->getCurrentAllowedLesson($userId, $courseId2) : 0;

                $response->json([
                    'success'            => false,
                    'message'            => 'AI Tutor chỉ hỗ trợ bài học đã được mở khoá. Hãy hoàn thành bài học trước.',
                    'redirect_lesson_id' => $redirectId,
                ], 403);
                return;
            }

            $result = $this->aiService->chatTutor($userId, $message, $lessonId, $courseId);
            $response->success("AI Tutor đã phản hồi", $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/ai/history
     * Xóa lịch sử chat AI Tutor cho bài học cụ thể
     * Body: { lesson_id }
     */
    public function clearHistory(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $lessonId = $request->input('lesson_id');
            $userId   = $request->user->sub;

            if (!$lessonId) throw new Exception("Thiếu ID bài học.");

            $repo = new \App\Repositories\AiRepository();
            $repo->clearHistory($userId, (int)$lessonId);

            $response->success("Đã xóa lịch sử hội thoại.");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
