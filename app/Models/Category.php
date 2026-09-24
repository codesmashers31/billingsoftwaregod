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
                ORDER BY c.id ASC";
        return $this->db->query($sql);
    }
}
