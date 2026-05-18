<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use Throwable;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register(Request $request, Response $response)
    {
        try {
            \App\Middlewares\RateLimitMiddleware::handle($request, $response, 5, 60, 'register');
            $data = $request->all();
            
            // Đảm bảo các tham số bắt buộc không bị trống và là chuỗi
            if (empty($data['username']) || empty($data['email']) || empty($data['password']) ||
                !is_string($data['username']) || !is_string($data['email']) || !is_string($data['password'])) {
                return $response->error("Vui lòng cung cấp đủ username, email và password hợp lệ.", 400);
            }

            $result = $this->authService->register($data);
            $response->success("Đăng ký thành công", $result, 201);
        } catch (Throwable $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function login(Request $request, Response $response)
    {
        try {
            \App\Middlewares\RateLimitMiddleware::handle($request, $response, 10, 60, 'login');
            $email = $request->input('email');
            $password = $request->input('password');

            if (!is_string($email) || !is_string($password) || empty($email) || empty($password)) {
                return $response->error("Email và password không được để trống và phải là chuỗi hợp lệ.", 400);
            }

            $result = $this->authService->login($email, $password);
            
            $response->success("Đăng nhập thành công", $result, 200);
        } catch (Throwable $e) {
            $response->error($e->getMessage(), 401);
        }
    }
}

