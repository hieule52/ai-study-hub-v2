<?php

namespace App\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTHandler
{
    private static function getSecret(): string
    {
        return $_ENV['JWT_SECRET'] ?? 'default_secret'; // Fallback nếu không có trong .env
    }

    public static function encode(array $payload): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (int)($_ENV['JWT_EXPIRATION'] ?? 7200);  // mặc định 2 giờ
        
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expirationTime;

        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function decode(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return null; // Token không hợp lệ hoặc hết hạn
        }
    }
}
