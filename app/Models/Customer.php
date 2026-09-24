<?php
namespace App\Models;

use App\Core\Model;

class Customer extends Model {
    protected string $table = 'customers';
    protected array $fillable = [
        'customer_code', 'name', 'mobile', 'email', 'address', 
        'city', 'state', 'pincode', 'gst_number', 'credit_limit', 
        'outstanding_balance', 'customer_type', 'status'
    ];

    public function search(string $term, int $limit = 10): array {
        $term = "%{$term}%";
        $sql = "SELECT * FROM `customers` 
                WHERE (name LIKE ? OR mobile LIKE ? OR customer_code LIKE ?) AND status = 'active' 
                ORDER BY name ASC LIMIT {$limit}";
        return $this->db->query($sql, [$term, $term, $term]);
    }

    public function getInvoices(int $customerId, int $limit = 20): array {
        $sql = "SELECT * FROM `invoices` WHERE customer_id = ? ORDER BY id DESC LIMIT {$limit}";
        return $this->db->query($sql, [$customerId]);
    }

    public function getPayments(int $customerId, int $limit = 20): array {
        $sql = "SELECT p.*, a.name as account_name FROM `payments` p 
                LEFT JOIN `accounts` a ON p.account_id = a.id 
                WHERE p.customer_id = ? ORDER BY p.id DESC LIMIT {$limit}";
        return $this->db->query($sql, [$customerId]);
    }
}
