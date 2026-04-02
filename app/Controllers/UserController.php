<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class UserController extends Controller
{
    private $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->userModel = new UserModel();
    }


    public function showRegister()
    {
        return $this->view("auth/register", [
            "error" => ""
        ]);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return header('Location: /register');
        }

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$username || !$email || !$password) {
            return $this->view("auth/register", [
                "error" => "Vui lòng điền đầy đủ thông tin!"
            ]);
        }

        $result = $this->userModel->register($username, $email, $password);

        if ($result['success']) {
            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            return header('Location: /login');
        }

        return $this->view("auth/register", [
            "error" => $result['message']
        ]);
    }

    public function showLogin()
    {
        // Unset success ở controller để tránh flash lại khi refresh (tùy chọn, view cũng có thể unset)
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        return $this->view("auth/login", [
            "success" => $success
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return header('Location: /login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            return $this->view("auth/login", [
                "error" => "Vui lòng điền đầy đủ thông tin!"
            ]);
        }

        $result = $this->userModel->login($email, $password);

        if ($result['success']) {
            $user = $result['user']; // Lấy user để dễ đọc code
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['avatar']   = $user['avatar'] ?: 'default.png';
            $_SESSION['is_admin'] = (int)$user['is_admin'];
            $_SESSION['is_vip']   = (int)$user['is_vip'];
            unset($_SESSION['success']);

            return header('Location: /');
        }

        return $this->view("auth/login", [
            "error" => $result['message']
        ]);
    }

    public function logout()
    {
        session_destroy();
        return header('Location: /');
    }
}
