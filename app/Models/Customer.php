<?php
namespace App\Models;

use App\Core\Model;

class Customer extends Model {
    protected string $table = 'customers';
    protected array $fillable = [
        'customer_code', 'name', 'mobile', 'email', 'address', 'shipping_address',
        'city', 'state', 'state_code', 'pincode', 'gst_number', 'credit_limit', 
        'outstanding_balance', 'customer_type', 'status'
    ];

    public function findOrCreate(array $data): array {
        $mobile = trim($data['mobile'] ?? '');
        $name = trim($data['name'] ?? '');

        if ($mobile !== '') {
            $existing = $this->findBy('mobile', $mobile);
            if ($existing) {
                return $existing;
            }
        }

        $code = 'CUST-' . str_pad((string)($this->count() + 1), 4, '0', STR_PAD_LEFT);
        while ($this->findBy('customer_code', $code)) {
            $code = 'CUST-' . rand(1000, 9999);
        }

        $data['customer_code'] = $code;
        $data['name'] = $name ?: 'Cash Customer';
        $data['mobile'] = $mobile ?: '9999999999';
        $data['status'] = $data['status'] ?? 'active';
        $id = $this->create($data);
        return $this->find((int)$id);
    }

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
