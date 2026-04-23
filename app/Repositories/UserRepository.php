<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        if ($data) {
            return new User((array)$data);
        }
        return null;
    }

    public function create(array $data): ?User
    {
        $sql = "INSERT INTO users (username, email, password_hash, role) 
                VALUES (:username, :email, :password_hash, :role)";
        
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
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1");
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
        $stmt = $this->db->query("SELECT id, username, email, role, is_vip, status, created_at, last_login 
                                  FROM users 
                                  WHERE deleted_at IS NULL 
                                  ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    public function countVipUsers(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE is_vip = 1 AND deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        // Cho phép sửa username, email, role, is_vip, status
        $sql = "UPDATE users SET 
                username = COALESCE(:username, username),
                email = COALESCE(:email, email),
                role = COALESCE(:role, role),
                is_vip = COALESCE(:is_vip, is_vip),
                status = COALESCE(:status, status)
                WHERE id = :id AND deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'username' => $data['username'] ?? null,
            'email' => $data['email'] ?? null,
            'role' => $data['role'] ?? null,
            'is_vip' => isset($data['is_vip']) ? (int)$data['is_vip'] : null,
            'status' => $data['status'] ?? null
        ]);
    }

    public function delete(int $id): bool
    {
        // Soft delete
        $stmt = $this->db->prepare("UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getVipPayments(int $limit = 50): array
    {
        $sql = "SELECT vp.*, u.username, u.email 
                FROM vip_payments vp 
                JOIN users u ON vp.user_id = u.id 
                ORDER BY vp.created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $stmt = $this->db->prepare("UPDATE users SET username = :username WHERE id = :id AND deleted_at IS NULL");
        return $stmt->execute([
            'username' => $username,
            'id' => $id
        ]);
    }

    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id AND deleted_at IS NULL");
        return $stmt->execute([
            'password_hash' => $hashedPassword,
            'id' => $id
        ]);
    }

    public function updateAvatar(int $id, string $avatarUrl): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET avatar = :avatar WHERE id = :id AND deleted_at IS NULL");
        return $stmt->execute([
            'avatar' => $avatarUrl,
            'id' => $id
        ]);
    }
}
