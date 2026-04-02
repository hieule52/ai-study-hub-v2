<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Groq;
use App\Models\ChatHistoryModel;

class AiChatController extends Controller
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
        $chatHistory = [];

        // Lấy user ID từ session
        $userId = $_SESSION['user_id'] ?? null;

        // Load lịch sử chat nếu user đã đăng nhập
        if ($userId) {
            $chatHistory = $this->chatHistoryModel->getHistoryByType($userId, 'chat');
        }

        $raw = file_get_contents("php://input");
        $json = json_decode($raw, true);

        if ($json && isset($json["message"])) {
            $userMessage = trim($json["message"]);
            if ($userMessage !== "") {
                // Gọi AI
                $response = Groq::ask($userMessage);

                // Lưu vào database nếu user đã đăng nhập
                if ($userId && $response) {
                    $this->chatHistoryModel->saveByType($userId, 'chat', $userMessage, $response);
                }

                echo $response;
                exit;
            }
        }

        return $this->view("ai/chat", [
            "response" => $response,
            "userMessage" => $userMessage,
            "chatHistory" => $chatHistory
        ]);
    }
}
