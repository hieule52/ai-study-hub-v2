<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Groq;
use App\Models\ChatHistoryModel;

class AiHomeworkController extends Controller
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
        $homeworkHistory = [];

        // Lấy user ID từ session
        $userId = $_SESSION['user_id'] ?? null;

        // Load lịch sử homework nếu user đã đăng nhập
        if ($userId) {
            $homeworkHistory = $this->chatHistoryModel->getHistoryByType($userId, 'homework');
        }

        // Xử lý AJAX request
        $raw = file_get_contents("php://input");
        $json = json_decode($raw, true);

        if ($json && isset($json["message"])) {
            $userMessage = trim($json["message"]);
            if ($userMessage !== "") {
                // System prompt cho giải bài tập
                $systemPrompt = "Bạn là một giáo viên AI chuyên giải bài tập cho học sinh. " .
                    "Hãy giải bài tập một cách chi tiết, từng bước, dễ hiểu. " .
                    "Giải thích rõ ràng lý do của mỗi bước. " .
                    "Luôn trả lời bằng tiếng Việt. " .
                    "Nếu là bài toán, hãy trình bày công thức và cách tính. " .
                    "Nếu là bài văn, hãy phân tích và hướng dẫn cách làm.";

                // Gọi AI với system prompt
                $response = Groq::ask($userMessage, $systemPrompt);

                // Lưu vào database nếu user đã đăng nhập
                if ($userId && $response) {
                    $this->chatHistoryModel->saveByType($userId, 'homework', $userMessage, $response);
                }

                echo $response;
                exit;
            }
        }

        return $this->view("ai/homework", [
            "response" => $response,
            "userMessage" => $userMessage,
            "homeworkHistory" => $homeworkHistory
        ]);
    }
}
