<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Groq;
use App\Models\ChatHistoryModel;

class AiQuizController extends Controller
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
        $quizHistory = [];

        // Lấy user ID từ session
        $userId = $_SESSION['user_id'] ?? null;

        // Load lịch sử quiz nếu user đã đăng nhập
        if ($userId) {
            $quizHistory = $this->chatHistoryModel->getHistoryByType($userId, 'quiz');
        }

        // Xử lý AJAX request
        $raw = file_get_contents("php://input");
        $json = json_decode($raw, true);

        if ($json && isset($json["message"])) {
            $userMessage = trim($json["message"]);
            if ($userMessage !== "") {
                // System prompt cho tạo quiz
                $systemPrompt = "Bạn là một giáo viên AI chuyên tạo câu hỏi trắc nghiệm. " .
                    "Hãy tạo 5-10 câu hỏi trắc nghiệm về chủ đề được yêu cầu. " .
                    "Mỗi câu hỏi phải có 4 đáp án (A, B, C, D) và chỉ rõ đáp án đúng. " .
                    "Format: Câu X: [Nội dung câu hỏi]\nA. [Đáp án A]\nB. [Đáp án B]\nC. [Đáp án C]\nD. [Đáp án D]\nĐáp án đúng: [A/B/C/D]\n\n" .
                    "Luôn trả lời bằng tiếng Việt. " .
                    "Câu hỏi phải rõ ràng, chính xác và phù hợp với trình độ học sinh.";

                // Gọi AI với system prompt
                $response = Groq::ask($userMessage, $systemPrompt);

                // Lưu vào database nếu user đã đăng nhập
                if ($userId && $response) {
                    $this->chatHistoryModel->saveByType($userId, 'quiz', $userMessage, $response);
                }

                echo $response;
                exit;
            }
        }

        return $this->view("ai/quiz", [
            "response" => $response,
            "userMessage" => $userMessage,
            "quizHistory" => $quizHistory
        ]);
    }
}
