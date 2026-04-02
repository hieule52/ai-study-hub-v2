<?php

namespace App\Core;

class Groq
{
    public static function ask($prompt, $systemPrompt = null)
    {
        if (empty($prompt)) {
            return "❗ Bạn chưa nhập nội dung.";
        }

        $apiKey = $_ENV["GROQ_API_KEY"] ?? null;

        if (!$apiKey) {
            return "❌ Lỗi: Chưa setup GROQ_API_KEY trong .env";
        }

        $url = "https://api.groq.com/openai/v1/chat/completions";

        // Default system prompt nếu không truyền vào
        if ($systemPrompt === null) {
            $systemPrompt = "Bạn là trợ lý AI học tập. Luôn trả lời bằng tiếng Việt.";
        }

        // Escape prompt ko sài cũng ok thì phải
        $escapedPrompt = str_replace(
            ["\\", "\"", "\n", "\r", "\t"],
            ["\\\\", "\\\"", "\\n", "\\r", "\\t"],
            $prompt
        );

        $data = [
            "model" => "llama-3.1-8b-instant",
            "messages" => [
                [
                    "role" => "system",
                    "content" => $systemPrompt
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ]
        ];

        $json = json_encode($data, JSON_UNESCAPED_UNICODE); // cho phép trả tiếng việt PHP → JSON

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer $apiKey"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_TIMEOUT => 30, //Nếu 30s chưa có kết quả → lỗi thôi ae
            // Fix SSL certificate error (for development only)
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            return "❌ CURL error: " . curl_error($ch);
        }

        curl_close($ch);

        $response = json_decode($result, true); //true trả về mảng

        if (isset($response["error"])) {
            return "❌ API Error: " . $response["error"]["message"];
        }

        return $response["choices"][0]["message"]["content"]
            ?? "❌ Không có câu trả lời từ AI.";
    }
}
// {
//   "id": "chatcmpl-12345",
//   "object": "chat.completion",
//   "created": 1733955000,
//   "model": "llama-3.1-8b-instant",
//   "choices": [
//     {
//       "index": 0,
//       "message": {
//         "role": "assistant",
//         "content": "Xin chào! Tôi là AI đây."
//       },
//       "finish_reason": "stop"
//     }
//   ]
// }