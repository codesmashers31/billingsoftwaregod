<?php
namespace App\Models;

use App\Core\Model;

class Supplier extends Model {
    protected string $table = 'suppliers';
    protected array $fillable = [
        'supplier_code', 'name', 'company_name', 'mobile', 'email', 
        'address', 'city', 'state', 'gst_number', 'bank_name', 
        'bank_account_no', 'bank_ifsc', 'outstanding_balance', 'status'
    ];

    public function getPurchases(int $supplierId, int $limit = 20): array {
        $sql = "SELECT * FROM `purchases` WHERE supplier_id = ? ORDER BY id DESC LIMIT {$limit}";
        return $this->db->query($sql, [$supplierId]);
    }

    public function getPayments(int $supplierId, int $limit = 20): array {
        $sql = "SELECT p.*, a.name as account_name FROM `payments` p 
                LEFT JOIN `accounts` a ON p.account_id = a.id 
                WHERE p.supplier_id = ? ORDER BY p.id DESC LIMIT {$limit}";
        return $this->db->query($sql, [$supplierId]);
    }
}
