<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class ProfileController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    // Trang profile
    public function index()
    {
        $user_id = $_SESSION['user_id'];
        $user = $this->userModel->getFullUser($user_id);

        if (!$user) {
            header('Location: /');
            exit;
        }

        // Cập nhật username trong session
        $_SESSION['username'] = $user['username'];

        return $this->view("profile", [
            "user" => $user,
            "success" => $_SESSION['success'] ?? null,
            "error" => null
        ]);
    }

    // Update profile
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $avatar_path = null;

        // Validate
        if (!$username || !$email) {
            return $this->reloadWithError("Vui lòng điền đầy đủ thông tin!");
        }

        // Upload avatar nếu có
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->handleAvatarUpload($user_id);

            if (!$upload['success']) {
                return $this->reloadWithError($upload['message']);
            }

            $avatar_path = $upload['filename'];
        }

        // Update db
        $update = $this->userModel->updateProfile($user_id, $username, $email, $avatar_path);

        if ($update['success']) {
            $_SESSION['success'] = $update['message'];
            header('Location: /profile');
            exit;
        }

        return $this->reloadWithError($update['message']);
    }


    /** ------------------------------------
     *  HÀM PHỤ: Reload profile với lỗi
     * -----------------------------------*/
    private function reloadWithError($msg)
    {
        $user = $this->userModel->getFullUser($_SESSION['user_id']);

        return $this->view("profile", [
            "user" => $user,
            "error" => $msg,
            "success" => null
        ]);
    }


    /** ------------------------------------
     *  HÀM PHỤ: Upload avatar
     * -----------------------------------*/
    private function handleAvatarUpload($user_id)
    {
        $file = $_FILES['avatar'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowed)) {
            return ['success' => false, 'message' => 'Chỉ chấp nhận ảnh JPG, PNG hoặc GIF!'];
        }

        if ($file['size'] > $max_size) {
            return ['success' => false, 'message' => 'File ảnh quá lớn! Tối đa 2MB.'];
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $user_id . '_' . time() . '.' . $ext;

        $upload_dir = __DIR__ . '/../../public/assets/avatars/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
            return ['success' => false, 'message' => 'Lỗi upload hình ảnh!'];
        }

        // Xóa avatar cũ nếu tồn tại
        $old = $this->userModel->getById($user_id);
        if ($old && $old['avatar'] && file_exists($upload_dir . $old['avatar'])) {
            unlink($upload_dir . $old['avatar']);
        }

        return [
            'success' => true,
            'filename' => $filename
        ];
    }
}
