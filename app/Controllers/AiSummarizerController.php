<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Groq;
use App\Models\ChatHistoryModel;

class AiSummarizerController extends Controller
{
    private $chatHistoryModel;

    public function __construct()
    {
        $this->chatHistoryModel = new ChatHistoryModel();
    }

    public function index()
    {
        $response = null;
        $userMessage = null;
        $summaryHistory = [];

        // Lấy user ID từ session
        $userId = $_SESSION['user_id'] ?? null;

        // Load lịch sử summarizer nếu user đã đăng nhập
        if ($userId) {
            $summaryHistory = $this->chatHistoryModel->getHistoryByType($userId, 'summarizer');
        }

        // Xử lý AJAX request
        $raw = file_get_contents("php://input");
        $json = json_decode($raw, true);

        if ($json && isset($json["message"])) {
            $userMessage = trim($json["message"]);
            if ($userMessage !== "") {
                // System prompt cho tóm tắt
                $systemPrompt = "Bạn là một trợ lý AI chuyên tóm tắt nội dung. " .
                    "Hãy tóm tắt nội dung được cung cấp một cách ngắn gọn, súc tích nhưng đầy đủ ý chính. " .
                    "Trình bày theo dạng bullet points hoặc đoạn văn ngắn, dễ hiểu. " .
                    "Luôn trả lời bằng tiếng Việt. " .
                    "Nếu nội dung quá dài, hãy chia thành các phần chính với tiêu đề rõ ràng. " .
                    "Tập trung vào những thông tin quan trọng nhất.";

                // Gọi AI với system prompt
                $response = Groq::ask($userMessage, $systemPrompt);

                // Lưu vào database nếu user đã đăng nhập
                if ($userId && $response) {
                    $this->chatHistoryModel->saveByType($userId, 'summarizer', $userMessage, $response);
                }

                echo $response;
                exit;
            }
        }

        return $this->view("ai/summarizer", [
            "response" => $response,
            "userMessage" => $userMessage,
            "summaryHistory" => $summaryHistory
        ]);
    }
}
