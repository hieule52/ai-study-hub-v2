<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Đăng ký user mới
    public function register($username, $email, $password)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Username hoặc email đã tồn tại!'];
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password_hash, created_at)
            VALUES (?, ?, ?, NOW())
        ");

        $result = $stmt->execute([$username, $email, $password_hash]);

        return $result
            ? ['success' => true, 'message' => 'Đăng ký thành công!']
            : ['success' => false, 'message' => 'Lỗi đăng ký!'];
    }

    // Login
    public function login($email, $password)
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email, password_hash, avatar,is_admin, is_vip
            FROM users
            WHERE email = ?
        ");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            return ['success' => true, 'user' => $user];
        }

        return ['success' => false, 'message' => 'Email hoặc mật khẩu sai!'];
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFullUser($id)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, COUNT(f.friend_id) AS friend_count
            FROM users u
            LEFT JOIN friendships f ON u.id = f.user_id AND f.status = 'accepted'
            WHERE u.id = ?
            GROUP BY u.id
        ");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $username, $email, $avatar = null)
    {
        $user = $this->getById($id);

        if ($username !== $user['username']) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $id]);
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Username đã tồn tại!'];
            }
        }

        if ($email !== $user['email']) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email đã tồn tại!'];
            }
        }

        // Build query update
        if ($avatar) {
            $sql = "UPDATE users SET username = ?, email = ?, avatar = ? WHERE id = ?";
            $params = [$username, $email, $avatar, $id];
        } else {
            $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            $params = [$username, $email, $id];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return ['success' => true, 'message' => 'Cập nhật thành công!'];
    }
}
