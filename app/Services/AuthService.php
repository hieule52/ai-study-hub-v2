<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Core\JWTHandler;
use Exception;

class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function register(array $data): array
    {
        if (empty($data['email']) || empty($data['password']) || empty($data['username'])) {
            throw new Exception("Vui lòng cung cấp đủ username, email và password.");
        }

        if ($this->userRepo->findByEmail($data['email'])) {
            throw new Exception("Email đã tồn tại trong hệ thống.");
        }

        if ($this->userRepo->findByUsername($data['username'])) {
            throw new Exception("Tên hiển thị này đã được sử dụng. Vui lòng chọn tên khác.");
        }

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        // Chỉ cho phép student hoặc teacher khi tự đăng ký (admin phải được cấp qua admin panel)
        $allowedRoles = ['student', 'teacher'];
        $role = in_array($data['role'] ?? '', $allowedRoles) ? $data['role'] : 'student';

        $user = $this->userRepo->create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'role' => $role
        ]);

        if (!$user) {
            throw new Exception("Có lỗi xảy ra khi tạo tài khoản.");
        }

        return $this->generateTokenPayload($user);
    }

    public function login(string $email, string $password): array
    {
        if (empty($email) || empty($password)) {
            throw new Exception("Email và password không được để trống.");
        }

        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user->password_hash)) {
            throw new Exception("Thông tin đăng nhập không chính xác.");
        }

        if ($user->status === 'banned') {
            throw new Exception("Tài khoản của bạn đã bị khóa.");
        }

        $this->userRepo->updateLastLogin($user->id);

        return $this->generateTokenPayload($user);
    }

    private function generateTokenPayload($user): array
    {
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'is_vip' => $user->is_vip
        ];

        $token = JWTHandler::encode($payload);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'is_vip' => $user->is_vip,
                'avatar' => $user->avatar
            ]
        ];
    }
}
