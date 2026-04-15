<?php

namespace App\Services;

use App\Repositories\VipPaymentRepository;
use Exception;

class VipPaymentService
{
    private VipPaymentRepository $paymentRepo;

    public function __construct()
    {
        $this->paymentRepo = new VipPaymentRepository();
    }

    public function generatePayment(int $userId): array
    {
        $amount = (float)($_ENV['VIP_AMOUNT_VND'] ?? 500000);
        $txnId = $this->paymentRepo->createTransaction($userId, $amount);

        if (!$txnId) {
            throw new Exception("Không thể tạo giao dịch. Vui lòng thử lại.");
        }

        // Mô phỏng chuỗi thanh toán mã QR của VNPAY/MOMO
        // Ví dụ: Nội dung CK: VIP PAY {TXN_ID}
        $paymentCode = "VIP_PAY_" . str_pad($txnId, 6, "0", STR_PAD_LEFT);

        return [
            'transaction_id' => $txnId,
            'amount' => $amount,
            'currency' => 'VND',
            'payment_code' => $paymentCode,
            'instruction' => 'Vui lòng chuyển khoản với nội dung: ' . $paymentCode,
            'bank_account' => '1234567890 (Ví dụ Bank)',
            'account_name' => 'AI STUDY HUB'
        ];
    }

    public function mockWebhookPaymentSuccess(int $userId, int $txnId): bool
    {
        $txn = $this->paymentRepo->findTransaction($txnId);
        if (!$txn) {
            throw new Exception("Không tồn tại giao dịch này.");
        }

        if ($txn['status'] !== 'pending') {
            throw new Exception("Giao dịch này đã được xử lý trước đó.");
        }

        if ($txn['user_id'] != $userId) {
            throw new Exception("Lỗi bảo mật: Giao dịch không khớp thông tin tài khoản.");
        }

        $success = $this->paymentRepo->completeTransaction($txnId, $userId);

        if (!$success) {
            throw new Exception("Lỗi khi xử lý DB, vui lòng liên hệ admin.");
        }

        return true;
    }
}
