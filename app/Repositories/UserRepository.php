<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;
use PDOException;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        if ($data) {
            return new User((array)$data);
        }
        return null;
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch();
        return $data ? new User((array)$data) : null;
    }

    public function create(array $data): ?User
    {
        $sql = "INSERT INTO users (username, email, password_hash, role) 
                VALUES (:username, :email, :password_hash, :role)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                'username'      => $data['username'],
                'email'         => $data['email'],
                'password_hash' => $data['password_hash'],
                'role'          => $data['role'] ?? 'student'
            ]);

            if ($success) {
                $id = $this->db->lastInsertId();
                return $this->findById($id);
            }
            return null;
        } catch (PDOException $e) {
            // Bắt lỗi duplicate key (SQLSTATE 23000)
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'username')) {
                    throw new \Exception("Tên hiển thị này đã được sử dụng. Vui lòng chọn tên khác.");
                }
                if (str_contains($e->getMessage(), 'email')) {
                    throw new \Exception("Email đã tồn tại trong hệ thống.");
                }
                throw new \Exception("Thông tin đã tồn tại trong hệ thống.");
            }
            throw $e;
        }
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new User((array)$data);
        }
        return null;
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->query("SELECT id, username, email, role, status, created_at, last_login 
                                  FROM users 
                                  WHERE deleted_at IS NULL
                                  ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Paginated + searchable user list for admin
     */
    public function getAllUsersPaginated(int $limit, int $offset, string $search = '', string $role = ''): array
    {
        $where = ["deleted_at IS NULL", "role != 'admin'"];
        $params = [];

        if ($search !== '') {
            $where[] = '(username LIKE :search OR email LIKE :search2)';
            $kw = '%' . $search . '%';
            $params[':search']  = $kw;
            $params[':search2'] = $kw;
        }
        if ($role !== '' && in_array($role, ['student', 'teacher', 'admin'])) {
            $where[] = 'role = :role';
            $params[':role'] = $role;
        }

        $sql = "SELECT id, username, email, role, status, created_at, last_login
                FROM users
                WHERE " . implode(' AND ', $where) . "
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count filtered users for pagination meta
     */
    public function countUsersFiltered(string $search = '', string $role = ''): int
    {
        $where = ["deleted_at IS NULL", "role != 'admin'"];
        $params = [];

        if ($search !== '') {
            $where[] = '(username LIKE :search OR email LIKE :search2)';
            $kw = '%' . $search . '%';
            $params[':search']  = $kw;
            $params[':search2'] = $kw;
        }
        if ($role !== '' && in_array($role, ['student', 'teacher', 'admin'])) {
            $where[] = 'role = :role';
            $params[':role'] = $role;
        }

        $sql = "SELECT COUNT(*) FROM users WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function updateRole(int $id, string $role): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET role = :role WHERE id = :id");
        return $stmt->execute(['role' => $role, 'id' => $id]);
    }

    public function countUsers(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND role != 'admin'");
        return (int) $stmt->fetchColumn();
    }

    public function countVipUsers(): int
    {
        // VIP system removed — returns 0
        return 0;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE users SET 
                username = COALESCE(:username, username),
                email = COALESCE(:email, email),
                role = COALESCE(:role, role),
                status = COALESCE(:status, status),
                password_hash = COALESCE(:password_hash, password_hash)
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'            => $id,
            'username'      => $data['username'] ?? null,
            'email'         => $data['email'] ?? null,
            'role'          => $data['role'] ?? null,
            'status'        => $data['status'] ?? null,
            'password_hash' => $data['password'] ?? null
        ]);
    }

    public function delete(int $id): bool
    {
        // Hard delete all related records first
        $tables = [
            "DELETE FROM lesson_progress WHERE user_id = :id",
            "DELETE FROM quiz_attempts WHERE user_id = :id",
            "DELETE FROM enrollments WHERE user_id = :id",
            "DELETE FROM messages WHERE sender_id = :id OR receiver_id = :id",
            "DELETE FROM notifications WHERE user_id = :id",
            "DELETE FROM certificates WHERE user_id = :id",
            "DELETE FROM course_reviews WHERE user_id = :id",
        ];

        foreach ($tables as $sql) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['id' => $id]);
            } catch (\PDOException $e) {
                // Skip if table doesn't exist
                if ($e->getCode() !== '42S02') {
                    throw $e;
                }
            }
        }

        // Soft delete the user
        $stmt = $this->db->prepare("UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getVipPayments(int $limit = 50): array
    {
        // VIP payments table removed — returns empty array
        return [];
    }

    public function getAuditLogs(int $limit = 50): array
    {
        $sql = "SELECT al.*, u.username, u.email 
                FROM audit_logs al 
                JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateProfile(int $id, string $username): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE users SET username = :username WHERE id = :id");
            return $stmt->execute([
                'username' => $username,
                'id' => $id
            ]);
        } catch (PDOException $e) {
            // Bắt lỗi duplicate key (SQLSTATE 23000)
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'username')) {
                    throw new \Exception("Tên hiển thị này đã được sử dụng. Vui lòng chọn tên khác.");
                }
            }
            throw $e;
        }
    }

    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
        return $stmt->execute([
            'password_hash' => $hashedPassword,
            'id' => $id
        ]);
    }

    public function updateAvatar(int $id, string $avatarUrl): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
        return $stmt->execute([
            'avatar' => $avatarUrl,
            'id' => $id
        ]);
    }
}
