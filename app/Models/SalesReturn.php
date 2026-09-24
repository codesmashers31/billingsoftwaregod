<?php
namespace App\Models;

use App\Core\Model;

class SalesReturn extends Model {
    protected string $table = 'sales_returns';
    protected array $fillable = [
        'return_no', 'invoice_id', 'customer_id', 'return_date', 
        'total_refund_amount', 'refund_method', 'reason', 'status', 'created_by'
    ];

    public function generateReturnNo(): string {
        $prefix = 'RET-' . date('Ym') . '-';
        $sql = "SELECT return_no FROM `sales_returns` WHERE return_no LIKE ? ORDER BY id DESC LIMIT 1";
        $last = $this->db->query($sql, [$prefix . '%']);
        if (!empty($last)) {
            $num = (int)substr($last[0]['return_no'], strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    public function getWithDetails(int $id): ?array {
        $sql = "SELECT sr.*, i.invoice_no, c.name as customer_name, c.mobile as customer_mobile, u.name as creator_name 
                FROM `sales_returns` sr 
                JOIN `invoices` i ON sr.invoice_id = i.id 
                JOIN `customers` c ON sr.customer_id = c.id 
                JOIN `users` u ON sr.created_by = u.id 
                WHERE sr.id = ? LIMIT 1";
        $res = $this->db->query($sql, [$id]);
        return !empty($res) ? $res[0] : null;
    }

    public function getItems(int $returnId): array {
        $sql = "SELECT sri.*, p.name as product_name, p.code as product_code, p.god_name, p.material 
                FROM `sales_return_items` sri 
                JOIN `products` p ON sri.product_id = p.id 
                WHERE sri.sales_return_id = ?";
        return $this->db->query($sql, [$returnId]);
    }
}
