<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use Exception;

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
            $result = $this->authService->register($data);
            $response->success("Đăng ký thành công", $result, 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function login(Request $request, Response $response)
    {
        try {
            \App\Middlewares\RateLimitMiddleware::handle($request, $response, 10, 60, 'login');
            $email = $request->input('email');
            $password = $request->input('password');

            $result = $this->authService->login($email, $password);
            
            $response->success("Đăng nhập thành công", $result, 200);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 401);
        }
    }
}
