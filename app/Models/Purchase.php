<?php
namespace App\Models;

use App\Core\Model;

class Purchase extends Model {
    protected string $table = 'purchases';
    protected array $fillable = [
        'purchase_no', 'supplier_id', 'purchase_date', 'subtotal', 
        'discount_amount', 'tax_amount', 'total_amount', 'paid_amount', 
        'due_amount', 'status', 'payment_status', 'notes', 'created_by', 
        'approved_by', 'approved_at'
    ];

    public function getWithDetails(int $id): ?array {
        $sql = "SELECT p.*, s.name as supplier_name, s.company_name, s.mobile as supplier_mobile, 
                       s.gst_number as supplier_gst, u.name as creator_name, a.name as approver_name 
                FROM `purchases` p 
                JOIN `suppliers` s ON p.supplier_id = s.id 
                JOIN `users` u ON p.created_by = u.id 
                LEFT JOIN `users` a ON p.approved_by = a.id 
                WHERE p.id = ? LIMIT 1";
        $res = $this->db->query($sql, [$id]);
        return !empty($res) ? $res[0] : null;
    }

    public function getItems(int $purchaseId): array {
        $sql = "SELECT pi.*, pr.name as product_name, pr.code as product_code, pr.sku, pr.god_name, pr.material 
                FROM `purchase_items` pi 
                JOIN `products` pr ON pi.product_id = pr.id 
                WHERE pi.purchase_id = ?";
        return $this->db->query($sql, [$purchaseId]);
    }

    public function generatePurchaseNo(): string {
        $prefix = 'PO-' . date('Ym') . '-';
        $sql = "SELECT purchase_no FROM `purchases` WHERE purchase_no LIKE ? ORDER BY id DESC LIMIT 1";
        $last = $this->db->query($sql, [$prefix . '%']);
        if (!empty($last)) {
            $num = (int)substr($last[0]['purchase_no'], strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }
}
