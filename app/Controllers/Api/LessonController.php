<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\LessonService;
use App\Middlewares\AuthMiddleware;
use Exception;

class LessonController
{
    private LessonService $lessonService;

    public function __construct()
    {
        $this->lessonService = new LessonService();
    }

    public function curriculum(Request $request, Response $response, string $courseId)
    {
        try {
            $authHeader = $request->getHeader('Authorization');
            $userRole = 'guest';
            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                $payload = \App\Core\JWTHandler::decode($token);
                if ($payload) {
                    $userRole = $payload->role ?? 'guest';
                }
            }

            $curriculum = $this->lessonService->getCourseCurriculum((int)$courseId, $userRole);
            $response->success("Giáo trình khóa học", $curriculum);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    public function show(Request $request, Response $response, string $lessonId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userRole = $request->user->role ?? 'guest';
            $lesson = $this->lessonService->getLessonDetail((int)$lessonId, $userRole);
            $response->success("Chi tiết bài giảng", $lesson);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    public function complete(Request $request, Response $response, string $lessonId)
    {
        try {
            AuthMiddleware::handle($request, $response);

            // === ROLE GUARD: Chỉ student mới được mark bài hoàn thành ===
            $userRole = $request->user->role ?? 'guest';
            if ($userRole !== 'student') {
                $response->error("Chỉ học viên mới có thể đánh dấu hoàn thành bài học.", 403);
                return;
            }

            $userId = (int)$request->user->sub;
            $percent = $this->lessonService->markLessonCompleted($userId, (int)$lessonId);
            $response->success("Đã lưu tiến độ hoàn thành bài học thành công.", ['progress' => $percent]);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}

