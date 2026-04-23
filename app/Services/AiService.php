<?php

namespace App\Services;

use App\Repositories\AiRepository;
use Exception;

class AiService
{
    private AiRepository $aiRepo;
    private string $geminiApiKey;

    public function __construct()
    {
        $this->aiRepo = new AiRepository();
        $this->geminiApiKey = $_ENV['GEMINI_API_KEY'] ?? '';
    }

    public function chat(int $userId, string $message, ?string $base64Image = null): array
    {
        if (empty($this->geminiApiKey)) {
            throw new Exception("Chưa cấu hình GEMINI_API_KEY.");
        }

        if (empty(trim($message))) {
            throw new Exception("Tin nhắn không được để trống.");
        }

        // Lấy lịch sử chat để AI có ngữ cảnh
        // Tùy chỉnh prompt tùy theo yêu cầu hệ thống LMS
        $prompt = "Bạn là một trợ lý ảo thông minh cho nền tảng AI Study Hub LMS. Hãy giúp học sinh giải đáp câu hỏi một cách dễ hiểu và tích cực.\nCâu hỏi: " . $message;

        $response = $this->callGeminiApi($prompt, $base64Image);

        if (!$response) {
            throw new Exception("Lỗi Gemini API: Trả về rỗng.");
        }
        if (str_starts_with($response, 'ERROR_CURL: ')) {
            throw new Exception($response);
        }
        if (str_starts_with($response, 'ERROR_JSON: ')) {
            throw new Exception("Lỗi API parse: " . $response);
        }

        // Lưu vào CSDL
        $this->aiRepo->saveInteraction($userId, $message, $response);

        return [
            'original_message' => $message,
            'ai_response' => $response
        ];
    }

    private function callGeminiApi(string $prompt, ?string $base64Image = null): ?string
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $this->geminiApiKey;
        
        $parts = [
            ['text' => $prompt]
        ];

        if ($base64Image) {
            $mimeType = 'image/jpeg';
            $rawData = $base64Image;

            if (preg_match('/^data:(image\/[a-zA-Z]+);base64,(.*)$/', $base64Image, $matches)) {
                $mimeType = $matches[1];
                $rawData = $matches[2];
            }

            $parts[] = [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => $rawData
                ]
            ];
        }

        $data = [
            'contents' => [
                [
                    'parts' => $parts
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        
        // Disable SSL cho Localhost Windows
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return 'ERROR_CURL: ' . $err;
        }

        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            return 'ERROR_JSON: ' . json_encode($result['error']);
        }
        
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
}
