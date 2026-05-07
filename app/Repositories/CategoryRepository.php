<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * CategoryRepository
 * CRUD cho danh mục khóa học (course_categories).
 * Hỗ trợ danh mục cha-con (parent_id).
 */
class CategoryRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Lấy tất cả categories (cây phẳng)
     */
    public function findAll(): array
    {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM courses co WHERE co.category_id = c.id AND co.deleted_at IS NULL) as course_count
                FROM course_categories c
                ORDER BY c.order_index ASC, c.name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả categories theo dạng tree (cha → con)
     */
    public function findAllAsTree(): array
    {
        $all = $this->findAll();
        return $this->buildTree($all);
    }

    /**
     * Build tree structure từ flat list
     */
    private function buildTree(array $items, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }

    /**
     * Tìm theo ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM course_categories WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Tìm theo slug
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM course_categories WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Tạo category mới
     */
    public function create(array $data): ?array
    {
        $sql = "INSERT INTO course_categories (name, slug, icon, parent_id, order_index) 
                VALUES (:name, :slug, :icon, :parent_id, :order_index)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? $this->generateSlug($data['name']),
            'icon' => $data['icon'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'order_index' => $data['order_index'] ?? 0
        ]);

        if ($success) {
            return $this->findById($this->db->lastInsertId());
        }
        return null;
    }

    /**
     * Cập nhật category
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE course_categories SET name = :name, slug = :slug, icon = :icon, 
                parent_id = :parent_id, order_index = :order_index WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'] ?? $this->generateSlug($data['name']),
            'icon' => $data['icon'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'order_index' => $data['order_index'] ?? 0
        ]);
    }

    /**
     * Xóa category
     */
    public function delete(int $id): bool
    {
        // Đặt courses về null category trước
        $stmt = $this->db->prepare("UPDATE courses SET category_id = NULL WHERE category_id = :id");
        $stmt->execute(['id' => $id]);

        // Xóa category
        $stmt = $this->db->prepare("DELETE FROM course_categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Sinh slug từ tên (Vietnamese-aware)
     */
    private function generateSlug(string $name): string
    {
        // Vietnamese characters mapping
        $vietnamese = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ',
            'À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
            'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
            'Ì','Í','Ị','Ỉ','Ĩ',
            'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
            'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
            'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
            'Đ'
        ];
        $ascii = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd',
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd'
        ];

        $slug = str_replace($vietnamese, $ascii, $name);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}
