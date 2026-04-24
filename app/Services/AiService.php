<?php

namespace App\Services;

use App\Repositories\AiRepository;
use Exception;

class AiService
{
    private AiRepository $aiRepo;
    private array $geminiApiKeys;
    private string $openAiApiKey;
    private string $groqApiKey;

    public function __construct()
    {
        $this->aiRepo = new AiRepository();
        $this->geminiApiKeys = array_filter(array_map('trim', explode(',', $_ENV['GEMINI_API_KEY'] ?? '')));
        $this->openAiApiKey = $_ENV['OPENAI_API_KEY'] ?? '';
        $this->groqApiKey = $_ENV['GROQ_API_KEY'] ?? '';
    }

    public function chat(int $userId, string $message, ?string $base64Image = null): array
    {
        if (empty(trim($message)) && empty($base64Image)) {
            throw new Exception("Tin nhắn và hình ảnh không được để trống.");
        }

        // Tùy chỉnh prompt tùy theo yêu cầu hệ thống LMS
        $prompt = "Bạn là một trợ lý ảo thông minh cho nền tảng AI Study Hub LMS. Hãy giúp học sinh giải đáp câu hỏi một cách dễ hiểu và tích cực.\nCâu hỏi: " . $message;

        // Xây dựng chiến lược Fallback Routing
        $providers = [];
        if ($base64Image) {
            // Có ảnh -> Bỏ qua Groq (không hỗ trợ)
            $providers = ['gemini', 'openai'];
        } else {
            // Không ảnh -> Groq (nhanh nhất) -> Gemini -> OpenAI
            $providers = ['groq', 'gemini', 'openai'];
        }

        $response = null;
        $lastError = "";

        foreach ($providers as $provider) {
            try {
                if ($provider === 'groq' && !empty($this->groqApiKey)) {
                    $response = $this->callGroqApi($prompt);
                } elseif ($provider === 'gemini' && !empty($this->geminiApiKeys)) {
                    $response = $this->callGeminiApi($prompt, $base64Image);
                } elseif ($provider === 'openai' && !empty($this->openAiApiKey)) {
                    $response = $this->callOpenAiApi($prompt, $base64Image);
                }

                // Nếu gọi thành công và kết quả không bắt đầu bằng ERROR_
                if ($response && !str_starts_with($response, 'ERROR_')) {
                    break; // Thành công! Dừng vòng lặp Fallback
                } else {
                    $lastError = $response ?: "Không có phản hồi";
                }
            } catch (Exception $e) {
                $lastError = $e->getMessage();
            }
        }

        if (!$response || str_starts_with($response, 'ERROR_')) {
            throw new Exception("Toàn bộ các Trợ lý AI (Groq, Gemini, GPT) đều đang quá tải hoặc hết hạn mức. Vui lòng thử lại sau. (Lỗi cuối: $lastError)");
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

        // Round-Robin API Keys nội bộ của Gemini
        foreach ($this->geminiApiKeys as $keyIndex => $geminiKey) {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $geminiKey;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);

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
                $errorCode = $result['error']['code'] ?? 0;
                $errorStatus = $result['error']['status'] ?? '';

                if ($errorCode == 429 || $errorStatus === 'RESOURCE_EXHAUSTED') {
                    if ($keyIndex === count($this->geminiApiKeys) - 1) {
                        return 'ERROR_JSON: Gemini Rate Limited';
                    }
                    continue;
                }

                if ($errorCode == 503 || $errorStatus === 'UNAVAILABLE') {
                    if ($keyIndex === count($this->geminiApiKeys) - 1) {
                        return 'ERROR_JSON: Gemini Unavailable';
                    }
                    continue;
                }

                return 'ERROR_JSON: ' . ($result['error']['message'] ?? json_encode($result['error']));
            }

            return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        return null;
    }

    private function callOpenAiApi(string $prompt, ?string $base64Image = null): ?string
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        $model = 'gpt-4o-mini';

        $userContent = $prompt;

        if ($base64Image) {
            $userContent = [
                [
                    "type" => "text",
                    "text" => $prompt
                ],
                [
                    "type" => "image_url",
                    "image_url" => [
                        "url" => $base64Image
                    ]
                ]
            ];
        }

        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $userContent]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->openAiApiKey,
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

        return $result['choices'][0]['message']['content'] ?? null;
    }

    private function callGroqApi(string $prompt): ?string
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $model = 'llama-3.1-8b-instant';

        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->groqApiKey,
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

        return $result['choices'][0]['message']['content'] ?? null;
    }
}
