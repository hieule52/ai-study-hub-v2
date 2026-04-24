<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\EnrollmentRepository;
use Exception;

class CassoWebhookController
{
    private EnrollmentRepository $enrollRepo;

    public function __construct()
    {
        $this->enrollRepo = new EnrollmentRepository();
    }

    public function handleCasso(Request $request, Response $response)
    {
        try {
            // Lấy Secure Token từ Header
            $headers = getallheaders();
            $secureToken = $headers['Secure-Token'] ?? ($headers['secure-token'] ?? '');

            $expectedToken = $_ENV['CASSO_API_KEY'] ?? '';

            if (empty($expectedToken)) {
                $response->error("Hệ thống chưa cấu hình Casso API Key.", 500);
                return;
            }

            if ($secureToken !== $expectedToken) {
                $response->error("Xác thực Webhook thất bại. Token không hợp lệ.", 401);
                return;
            }

            // Lấy payload
            $payload = json_decode(file_get_contents('php://input'), true);

            if (!isset($payload['data']) || !is_array($payload['data'])) {
                $response->error("Payload không hợp lệ.", 400);
                return;
            }

            // Xử lý từng giao dịch
            foreach ($payload['data'] as $transaction) {
                $description = strtoupper($transaction['description'] ?? '');
                $amount = (float)($transaction['amount'] ?? 0);

                // Regex bắt cú pháp: MUA {course_id} {user_id}
                // Ví dụ: MUA 1 5
                if (preg_match('/MUA\s+(\d+)\s+(\d+)/', $description, $matches)) {
                    $courseId = (int)$matches[1];
                    $userId = (int)$matches[2];

                    // Bỏ qua check giá tiền tạm thời, chỉ cần đúng cú pháp là Mở Khóa
                    // Trong thực tế, bạn nên query giá khóa học để so khớp với $amount
                    
                    $this->enrollRepo->enroll($userId, $courseId);
                }
            }

            // Trả về 200 OK để báo Casso biết đã nhận thành công (nếu không Casso sẽ gửi lại)
            $response->success("Webhook processed successfully.");

        } catch (Exception $e) {
            $response->error("Webhook Error: " . $e->getMessage(), 500);
        }
    }
}
