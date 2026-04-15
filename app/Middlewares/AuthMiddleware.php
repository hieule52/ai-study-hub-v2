<?php

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;
use App\Core\JWTHandler;

class AuthMiddleware
{
    public static function handle(Request $request, Response $response)
    {
        $authHeader = $request->getHeader('Authorization');
        
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->error('Unauthorized - Token missing', 401);
        }

        $token = $matches[1];
        $payload = JWTHandler::decode($token);

        if (!$payload) {
            $response->error('Unauthorized - Invalid or expired token', 401);
        }

        // Đưa thông tin tài khoản đang login vào request để controller dễ lấy
        $request->user = $payload;

        return true;
    }
}
