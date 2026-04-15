<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\VipPaymentService;
use App\Middlewares\AuthMiddleware;
use Exception;

class VipPaymentController
{
    private VipPaymentService $vipService;

    public function __construct()
    {
        $this->vipService = new VipPaymentService();
    }

    public function createPayment(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            
            $userId = $request->user->sub; // Lấy ID tài khoản đang request

            if ($request->user->is_vip == 1) {
                $response->error("Tài khoản của bạn đã là VIP.", 400);
            }

            $paymentInfo = $this->vipService->generatePayment($userId);
            
            $response->success("Đã khởi tạo yêu cầu thanh toán", $paymentInfo, 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function mockSuccess(Request $request, Response $response, string $txnId)
    {
        // Sandbox API: Giả lập Sandbox báo Bank chuyển khoản thành công
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub; 

            $this->vipService->mockWebhookPaymentSuccess($userId, (int)$txnId);
            
            $response->success("Sandbox: Thanh toán thành công! Tài khoản được nâng cấp VIP.");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
