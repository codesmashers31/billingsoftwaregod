<?php
namespace App\Models;

use App\Core\Model;

class SubCategory extends Model {
    protected string $table = 'subcategories';
    protected array $fillable = ['category_id', 'name', 'code', 'description', 'image', 'status'];

    public function getWithCategory(): array {
        $sql = "SELECT s.*, c.name as category_name, COUNT(p.id) as product_count 
                FROM `subcategories` s 
                JOIN `categories` c ON s.category_id = c.id 
                LEFT JOIN `products` p ON s.id = p.subcategory_id 
                GROUP BY s.id 
                ORDER BY c.name ASC, s.name ASC";
        return $this->db->query($sql);
    }

    public function getByCategory(int $categoryId): array {
        $sql = "SELECT * FROM `subcategories` WHERE category_id = ? AND status = 'active' ORDER BY name ASC";
        return $this->db->query($sql, [$categoryId]);
    }
}
