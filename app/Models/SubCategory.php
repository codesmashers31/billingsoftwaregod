<?php
namespace App\Models;

use App\Core\Model;

class SubCategory extends Model {
    protected string $table = 'subcategories';
    protected array $fillable = ['category_id', 'name', 'code', 'description', 'image', 'status'];

    public function getWithCategory(): array {
        $sql = "SELECT s.*, COALESCE(c.name, 'Uncategorized') as category_name, COUNT(p.id) as product_count 
                FROM `subcategories` s 
                LEFT JOIN `categories` c ON s.category_id = c.id 
                LEFT JOIN `products` p ON s.id = p.subcategory_id 
                GROUP BY s.id 
                ORDER BY s.id DESC";
        return $this->db->query($sql);
    }

    public function getByCategory(int $categoryId): array {
        $sql = "SELECT * FROM `subcategories` WHERE category_id = ? AND status = 'active' ORDER BY name ASC";
        return $this->db->query($sql, [$categoryId]);
    }

    public function findOrCreate(string $nameOrId, int $categoryId, string $status = 'active'): ?array {
        $name = trim($nameOrId);
        if ($name === '') {
            return null;
        }

        if (is_numeric($name)) {
            $sub = $this->find((int)$name);
            if ($sub && (int)$sub['category_id'] === $categoryId) {
                return $sub;
            }
        }

        $sql = "SELECT * FROM `subcategories` WHERE category_id = ? AND LOWER(name) = LOWER(?) LIMIT 1";
        $res = $this->db->query($sql, [$categoryId, $name]);
        if (!empty($res)) {
            return $res[0];
        }

        $code = 'SUB-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name) ?: 'SUB', 0, 3)) . '-' . rand(1000, 9999);
        while ($this->findBy('code', $code)) {
            $code = 'SUB-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name) ?: 'SUB', 0, 3)) . '-' . rand(1000, 9999);
        }

        try {
            $id = $this->create([
                'category_id' => $categoryId,
                'name' => $name,
                'code' => $code,
                'description' => '',
                'status' => $status
            ]);

            return $this->find((int)$id) ?: ['id' => $id, 'category_id' => $categoryId, 'name' => $name, 'code' => $code, 'status' => $status];
        } catch (\Throwable $e) {
            $res = $this->db->query("SELECT * FROM `subcategories` WHERE category_id = ? AND LOWER(name) = LOWER(?) LIMIT 1", [$categoryId, $name]);
            if (!empty($res)) {
                return $res[0];
            }
            throw $e;
        }
    }
}
