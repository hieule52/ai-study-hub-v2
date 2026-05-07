<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\VideoService;
use App\Middlewares\AuthMiddleware;
use Exception;

/**
 * VideoStreamController
 * API endpoints cho video streaming bảo mật.
 * 
 * Flow:
 * 1. GET /api/video/token/:lessonId → Tạo signed token (yêu cầu JWT + enrolled)
 * 2. GET /api/video/stream?token=xxx → Stream video (chỉ cần token hợp lệ)
 */
class VideoStreamController
{
    private VideoService $videoService;

    public function __construct()
    {
        $this->videoService = new VideoService();
    }

    /**
     * Tạo signed token để xem video
     * 
     * GET /api/video/token/:lessonId
     * Headers: Authorization: Bearer {jwt}
     * Query: course_id (required)
     * 
     * Response: { token, expires_at, stream_url }
     */
    public function getToken(Request $request, Response $response, string $lessonId)
    {
        try {
            AuthMiddleware::handle($request, $response);

            $userId = $request->user->sub;
            $userRole = $request->user->role ?? 'student';
            $courseId = (int) $request->input('course_id');

            if (!$courseId) {
                throw new Exception("Thiếu course_id.");
            }

            $tokenData = $this->videoService->generateStreamToken($userId, (int)$lessonId, $courseId, $userRole);
            $response->success("Token created", $tokenData);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 403);
        }
    }

    /**
     * Stream video qua signed token
     * 
     * GET /api/video/stream?token=xxx
     * Không cần JWT header → token đã chứa thông tin xác thực
     * Hỗ trợ HTTP Range cho video seeking
     */
    public function stream(Request $request, Response $response)
    {
        try {
            $token = $request->input('token');

            if (empty($token)) {
                throw new Exception("Thiếu video token.");
            }

            // VideoService xử lý validate token + stream file
            // Hàm này sẽ output trực tiếp và exit
            $this->videoService->streamVideo($token);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 403);
        }
    }
}
