<?php

namespace App\Models;

class User
{
    public int $id;
    public string $username;
    public string $email;
    public string $password_hash;
    public ?string $avatar;
    public string $role;
    public int $is_vip;
    public string $status;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;
    public ?int $deleted_by;
    public ?string $last_login;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? 0;
            $this->username = $data['username'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->password_hash = $data['password_hash'] ?? '';
            $this->avatar = $data['avatar'] ?? null;
            $this->role = $data['role'] ?? 'student';
            $this->is_vip = $data['is_vip'] ?? 0;
            $this->status = $data['status'] ?? 'active';
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
            $this->deleted_at = $data['deleted_at'] ?? null;
            $this->deleted_by = $data['deleted_by'] ?? null;
            $this->last_login = $data['last_login'] ?? null;
        }
    }
}
