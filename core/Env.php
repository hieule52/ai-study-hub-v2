<?php

namespace App\Core;

class Env
{
    public static function load($path)
    {
        // 1. Tải các biến môi trường từ tệp .env nếu tồn tại
        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                // Bỏ qua comment
                if (str_starts_with(trim($line), '#')) continue;

                $parts = explode('=', $line, 2);

                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);

                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }

        // 2. Tự động đồng bộ các biến môi trường hệ thống (System Environment Variables) vào $_ENV.
        // Điều này cực kỳ quan trọng khi chạy trong Docker/Container, nơi các biến được truyền trực tiếp
        // qua docker-compose.yml hoặc docker run mà không cần tệp .env vật lý trên ổ đĩa.
        $keys = [
            'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS',
            'APP_ENV', 'APP_URL',
            'JWT_SECRET', 'JWT_EXPIRATION',
            'GROQ_API_KEY', 'GEMINI_API_KEY', 'OPENAI_API_KEY',
            'CASSO_API_KEY', 'VIP_SECRET', 'VIP_AMOUNT_VND',
            'VIDEO_STORAGE_PATH', 'VIDEO_TOKEN_SECRET', 'VIDEO_TOKEN_EXPIRY', 'VIDEO_MAX_SIZE_MB',
            'WS_PORT'
        ];

        foreach ($keys as $key) {
            if (!isset($_ENV[$key]) || $_ENV[$key] === '') {
                $value = getenv($key);
                if ($value !== false) {
                    $_ENV[$key] = $value;
                }
            }
        }
    }
}

