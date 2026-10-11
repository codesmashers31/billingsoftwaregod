<?php
namespace App\Models;

use App\Core\Model;

class Category extends Model {
    protected string $table = 'categories';
    protected array $fillable = ['name', 'code', 'description', 'image', 'status'];

    public function getWithCounts(): array {
        $sql = "SELECT c.*, 
                       COUNT(DISTINCT s.id) as subcategory_count,
                       COUNT(DISTINCT p.id) as product_count,
                       COALESCE(SUM(p.current_stock), 0) as total_stock
                FROM `categories` c 
                LEFT JOIN `subcategories` s ON c.id = s.category_id 
                LEFT JOIN `products` p ON c.id = p.category_id 
                GROUP BY c.id 
                ORDER BY c.id DESC";
        return $this->db->query($sql);
    }

    public function findOrCreate(string $nameOrId, string $status = 'active'): array {
        $name = trim($nameOrId);
        if ($name === '') {
            $name = 'General';
        }

        if (is_numeric($name)) {
            $cat = $this->find((int)$name);
            if ($cat) {
                return $cat;
            }
        }

        $sql = "SELECT * FROM `categories` WHERE LOWER(name) = LOWER(?) LIMIT 1";
        $res = $this->db->query($sql, [$name]);
        if (!empty($res)) {
            return $res[0];
        }

        $code = 'CAT-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name) ?: 'CAT', 0, 3)) . '-' . rand(1000, 9999);
        while ($this->findBy('code', $code)) {
            $code = 'CAT-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name) ?: 'CAT', 0, 3)) . '-' . rand(1000, 9999);
        }

        try {
            $id = $this->create([
                'name' => $name,
                'code' => $code,
                'description' => '',
                'status' => $status
            ]);
            return $this->find((int)$id) ?: ['id' => $id, 'name' => $name, 'code' => $code, 'status' => $status];
        } catch (\Throwable $e) {
            $res = $this->db->query("SELECT * FROM `categories` WHERE LOWER(name) = LOWER(?) LIMIT 1", [$name]);
            if (!empty($res)) {
                return $res[0];
            }
            throw $e;
        }
    }
}
