<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model {
    protected string $table = 'products';
    protected array $fillable = [
        'name', 'code', 'sku', 'barcode', 'category_id', 'subcategory_id', 'brand',
        'description', 'god_name', 'material', 'color', 'height', 'width', 'length',
        'weight', 'size', 'finish_type', 'purchase_price', 'selling_price',
        'wholesale_price', 'discount_percent', 'gst_percent', 'hsn_code',
        'current_stock', 'min_stock', 'max_stock', 'stock_location', 'image', 'status',
        'created_by', 'updated_by'
    ];

    public function getWithDetails(int $id): ?array {
        $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name 
                FROM `products` p 
                LEFT JOIN `categories` c ON p.category_id = c.id 
                LEFT JOIN `subcategories` s ON p.subcategory_id = s.id 
                WHERE p.id = ? LIMIT 1";
        $res = $this->db->query($sql, [$id]);
        return !empty($res) ? $res[0] : null;
    }

    public function getPaginatedList(int $page = 1, int $perPage = 15, array $filters = []): array {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $term = "%{$filters['search']}%";
            $conditions[] = "(p.name LIKE ? OR p.code LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ? OR p.god_name LIKE ? OR p.material LIKE ?)";
            $params = array_merge($params, [$term, $term, $term, $term, $term, $term]);
        }

        if (!empty($filters['category_id'])) {
            $conditions[] = "p.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['subcategory_id'])) {
            $conditions[] = "p.subcategory_id = ?";
            $params[] = (int)$filters['subcategory_id'];
        }

        if (!empty($filters['material'])) {
            $conditions[] = "p.material = ?";
            $params[] = $filters['material'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = "p.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['low_stock']) && $filters['low_stock'] === '1') {
            $conditions[] = "p.current_stock <= p.min_stock";
        }

        $whereClause = implode(' AND ', $conditions);
        $offset = max(0, ($page - 1) * $perPage);

        $countSql = "SELECT COUNT(*) as total FROM `products` p WHERE {$whereClause}";
        $totalRes = $this->db->query($countSql, $params);
        $total = isset($totalRes[0]['total']) ? (int)$totalRes[0]['total'] : 0;

        $orderBy = $filters['order_by'] ?? 'p.id DESC';
        $dataSql = "SELECT p.*, c.name as category_name, s.name as subcategory_name 
                    FROM `products` p 
                    LEFT JOIN `categories` c ON p.category_id = c.id 
                    LEFT JOIN `subcategories` s ON p.subcategory_id = s.id 
                    WHERE {$whereClause} 
                    ORDER BY {$orderBy} 
                    LIMIT {$perPage} OFFSET {$offset}";
        $rows = $this->db->query($dataSql, $params);

        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    public function searchForPos(string $keyword, ?int $categoryId = null, int $limit = 24): array {
        $conditions = ["p.status = 'active'"];
        $params = [];

        if (!empty($keyword)) {
            $term = "%{$keyword}%";
            $conditions[] = "(p.name LIKE ? OR p.barcode = ? OR p.sku LIKE ? OR p.code LIKE ? OR p.god_name LIKE ? OR p.material LIKE ?)";
            $params = array_merge($params, [$term, $keyword, $term, $term, $term, $term]);
        }

        if (!empty($categoryId)) {
            $conditions[] = "p.category_id = ?";
            $params[] = $categoryId;
        }

        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT p.id, p.name, p.code, p.sku, p.barcode, p.god_name, p.material, p.height, p.size,
                       p.purchase_price, p.selling_price, p.wholesale_price, p.discount_percent, 
                       p.gst_percent, p.hsn_code, p.current_stock, p.min_stock, p.image, c.name as category_name 
                FROM `products` p 
                LEFT JOIN `categories` c ON p.category_id = c.id 
                WHERE {$whereClause} 
                ORDER BY p.name ASC 
                LIMIT {$limit}";

        return $this->db->query($sql, $params);
    }

    public function findByBarcodeOrSku(string $code): ?array {
        $sql = "SELECT * FROM `products` WHERE (barcode = ? OR sku = ? OR code = ?) AND status = 'active' LIMIT 1";
        $res = $this->db->query($sql, [$code, $code, $code]);
        return !empty($res) ? $res[0] : null;
    }

    public function getLowStockProducts(int $limit = 10): array {
        $sql = "SELECT p.*, c.name as category_name 
                FROM `products` p 
                LEFT JOIN `categories` c ON p.category_id = c.id 
                WHERE p.current_stock <= p.min_stock AND p.status = 'active' 
                ORDER BY p.current_stock ASC 
                LIMIT {$limit}";
        return $this->db->query($sql);
    }

    public function getDistinctMaterials(): array {
        $sql = "SELECT DISTINCT material FROM `products` WHERE material IS NOT NULL AND material != '' ORDER BY material ASC";
        $rows = $this->db->query($sql);
        return array_column($rows, 'material');
    }
}
