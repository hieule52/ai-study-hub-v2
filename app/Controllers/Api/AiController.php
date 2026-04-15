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

    public function chat(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            
            // Check nếu rule project: chỉ VIP mới được dùng AI
            // if ($request->user->is_vip == 0) {
            //     $response->error("Bạn cần nâng cấp VIP để dùng tính năng AI.", 403);
            // }

            $message = $request->input('message');
            $userId = $request->user->sub;

            $result = $this->aiService->chat($userId, $message);
            
            $response->success("AI đã phân tích", $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
