<?php

namespace App\Services;

use App\Repositories\AiRepository;
use Exception;

class AiService
{
    private AiRepository $aiRepo;
    private string $apiKey;

    public function __construct()
    {
        $this->aiRepo = new AiRepository();
        $this->apiKey = $_ENV['GROQ_API_KEY'] ?? '';
    }

    public function chat(int $userId, string $message): array
    {
        if (empty($this->apiKey)) {
            throw new Exception("Chưa cấu hình GROQ_API_KEY.");
        }

        if (empty(trim($message))) {
            throw new Exception("Tin nhắn không được để trống.");
        }

        // Lấy lịch sử chat để AI có ngữ cảnh
        // Tùy chỉnh prompt tùy theo yêu cầu hệ thống LMS
        $prompt = "Bạn là một trợ lý ảo thông minh cho nền tảng AI Study Hub LMS. Hãy giúp học sinh giải đáp câu hỏi một cách dễ hiểu và tích cực.\nCâu hỏi: " . $message;

        $response = $this->callGroqApi($prompt);

        if (!$response) {
            throw new Exception("Có lỗi khi giao tiếp với AI API.");
        }

        // Lưu vào CSDL
        $this->aiRepo->saveInteraction($userId, $message, $response);

        return [
            'original_message' => $message,
            'ai_response' => $response
        ];
    }

    private function callGroqApi(string $prompt): ?string
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';
        
        $data = [
            'model' => 'llama3-8b-8192', // Model phổ biến, tốc độ cao của Groq
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        // Timeout 30 giây
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return null;
        }

        $result = json_decode($response, true);
        
        return $result['choices'][0]['message']['content'] ?? null;
    }
}
