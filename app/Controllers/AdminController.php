<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class AdminController extends Controller
{
    private function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $isAdmin = (int)($_SESSION['is_admin'] ?? 0);
        if ($isAdmin !== 1) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }
    }

    /**
     * Render view con bên trong layout admin
     * Bạn cần tạo layout ở: app/Views/admin/layout/admin_layout.php
     */
    private function renderAdmin(string $viewFile, array $data = []): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // biến truyền xuống view
        extract($data);

        // đường dẫn view con
        $contentView = dirname(__DIR__) . "/Views/{$viewFile}.php";

        // layout admin
        $layout = dirname(__DIR__) . "/Views/admin/layout/admin_layout.php";

        if (!file_exists($layout)) {
            // fallback: nếu chưa có layout thì render thẳng view con
            require $contentView;
            return;
        }

        require $layout;
    }

    // ===== PAGES =====

    public function users(): void
    {
        $this->requireAdmin();

        $db = Database::connect();
        $users = $db->query("SELECT id, username, email, is_admin, is_vip, created_at FROM users ORDER BY id DESC")
                    ->fetchAll(\PDO::FETCH_ASSOC);

        $this->renderAdmin('admin/users', [
            'active' => 'users',
            'users'  => $users,
        ]);
    }

    public function vip(): void
    {
        $this->requireAdmin();

        $db = Database::connect();
        $vipUsers = $db->query("
            SELECT id, username, email, is_vip, created_at
            FROM users
            WHERE is_vip = 1
            ORDER BY created_at DESC
            ")->fetchAll(\PDO::FETCH_ASSOC);


        $this->renderAdmin('admin/vip', [
            'active'   => 'vip',
            'vipUsers' => $vipUsers,
        ]);
    }

    public function payments(): void
    {
        $this->requireAdmin();

        $db = Database::connect();

        $payments = [];
        try {
            $payments = $db->query("SELECT id, user_id, amount, description, status, created_at FROM payments ORDER BY id DESC")
                           ->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            $payments = [];
        }

        $this->renderAdmin('admin/payments', [
            'active'   => 'payments',
            'payments' => $payments,
        ]);
    }

    public function settings(): void
    {
        $this->requireAdmin();

        $this->renderAdmin('admin/settings', [
            'active' => 'settings',
        ]);
    }

    // ===== ACTIONS =====

    public function updateRole(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        $isAdmin = (int)($_POST['is_admin'] ?? 0); // 0 or 1

        if ($id <= 0 || !in_array($isAdmin, [0,1], true)) {
            echo json_encode(['ok' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->execute([$isAdmin, $id]);

        echo json_encode(['ok' => true]);
    }


    public function toggleVip(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'message' => 'ID không hợp lệ']);
            return;
        }

        $db = Database::connect();

        // Lấy trạng thái VIP hiện tại
        $stmt = $db->prepare("SELECT is_vip FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $isVip = (int)$stmt->fetchColumn();

        $new = $isVip === 1 ? 0 : 1;

        // Update
        $stmt2 = $db->prepare("UPDATE users SET is_vip = ? WHERE id = ?");
        $stmt2->execute([$new, $id]);

        echo json_encode(['ok' => true, 'is_vip' => $new]);
    }

    public function deleteUser(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'message' => 'ID không hợp lệ']);
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['ok' => true]);
    }

    public function saveSettings(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        // TODO: lưu settings vào DB hoặc file config
        echo json_encode(['ok' => true]);
    }

    public function management(): void
    {
        $this->requireAdmin();
        header('Location: /admin/users');
        exit;
    }

    public function createUser(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $username = trim((string)($_POST['username'] ?? ''));
        $email    = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $isAdmin  = (int)($_POST['is_admin'] ?? 0);
        $isVip    = (int)($_POST['is_vip'] ?? 0);

        if ($username === '' || $email === '' || $password === '') {
            echo json_encode(['ok' => false, 'message' => 'Vui lòng nhập đủ username, email, password']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => false, 'message' => 'Email không hợp lệ']);
            return;
        }

        $isAdmin = $isAdmin === 1 ? 1 : 0;
        $isVip   = $isVip === 1 ? 1 : 0;

        $db = Database::connect();

        // check trùng email
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ((int)$stmt->fetchColumn() > 0) {
            echo json_encode(['ok' => false, 'message' => 'Email đã tồn tại']);
            return;
        }

        // check trùng username
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ((int)$stmt->fetchColumn() > 0) {
            echo json_encode(['ok' => false, 'message' => 'Username đã tồn tại']);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $avatar = 'default.png';

        $stmt = $db->prepare("
            INSERT INTO users (username, email, password_hash, avatar, is_admin, is_vip, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$username, $email, $passwordHash, $avatar, $isAdmin, $isVip]);

        echo json_encode(['ok' => true]);
    }

    public function editUser(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $id       = (int)($_POST['id'] ?? 0);
        $username = trim((string)($_POST['username'] ?? ''));
        $email    = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? ''); // optional
        $isAdmin  = (int)($_POST['is_admin'] ?? 0);
        $isVip    = (int)($_POST['is_vip'] ?? 0);

        if ($id <= 0 || $username === '' || $email === '') {
            echo json_encode(['ok' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => false, 'message' => 'Email không hợp lệ']);
            return;
        }

        $isAdmin = $isAdmin === 1 ? 1 : 0;
        $isVip   = $isVip === 1 ? 1 : 0;

        $db = Database::connect();

        // check trùng email (trừ chính nó)
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?");
        $stmt->execute([$email, $id]);
        if ((int)$stmt->fetchColumn() > 0) {
            echo json_encode(['ok' => false, 'message' => 'Email đã tồn tại']);
            return;
        }

        // check trùng username
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id <> ?");
        $stmt->execute([$username, $id]);
        if ((int)$stmt->fetchColumn() > 0) {
            echo json_encode(['ok' => false, 'message' => 'Username đã tồn tại']);
            return;
        }

        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET username=?, email=?, password_hash=?, is_admin=?, is_vip=? WHERE id=?");
            $stmt->execute([$username, $email, $passwordHash, $isAdmin, $isVip, $id]);
        } else {
            $stmt = $db->prepare("UPDATE users SET username=?, email=?, is_admin=?, is_vip=? WHERE id=?");
            $stmt->execute([$username, $email, $isAdmin, $isVip, $id]);
        }

        echo json_encode(['ok' => true]);
    }
    // GỠ VIP
    public function removeVip(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'message' => 'ID không hợp lệ']);
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("UPDATE users SET is_vip = 0 WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['ok' => true]);
    }

    // CẤP VIP
    public function addVip(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'message' => 'ID không hợp lệ']);
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("UPDATE users SET is_vip = 1 WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['ok' => true]);
    }


}
