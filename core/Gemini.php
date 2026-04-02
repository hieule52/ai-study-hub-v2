<?php

namespace App\Core;

class Gemini
{
    public static function ask($prompt)
    {
        if (empty($prompt)) {
            return "❗ Bạn chưa nhập nội dung.";
        }

        $apiKey = $_ENV["GEMINI_API_KEY"] ?? null;

        if (!$apiKey) {
            return "❌ Lỗi: Chưa setup GEMINI_API_KEY trong .env";
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => "Bạn là trợ lý AI học tập. Luôn trả lời bằng tiếng Việt."],
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "x-goog-api-key: $apiKey"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_TIMEOUT => 15,
            // Fix SSL certificate error (for development only)
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            return "❌ CURL error: " . curl_error($ch);
        }

        curl_close($ch);

        $response = json_decode($result, true);

        if (isset($response["error"])) {
            return "❌ API Error: " . $response["error"]["message"];
        }

        return $response["candidates"][0]["content"]["parts"][0]["text"]
            ?? "❌ Không có câu trả lời từ AI.";
    }
}
