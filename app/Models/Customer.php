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

    /**
     * Generate unique sequential customer code e.g. CUST-0009
     */
    public function generateCustomerCode(): string {
        $maxId = (int)($this->db->query("SELECT MAX(id) as max_id FROM `customers`")[0]['max_id'] ?? 0);
        $next = max($maxId + 1, $this->count() + 1);
        $code = 'CUST-' . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
        while ($this->findBy('customer_code', $code)) {
            $code = 'CUST-' . rand(1000, 9999);
        }
        return $code;
    }

    /**
     * Intercept customer creation to guarantee unique customer_code is never empty
     */
    public function create(array $data): int|string {
        if (empty($data['customer_code'])) {
            $data['customer_code'] = $this->generateCustomerCode();
        }
        return parent::create($data);
    }

    public function findOrCreate(array $data): array {
        $mobile = trim($data['mobile'] ?? '');
        $name = trim($data['name'] ?? '');

        if ($mobile !== '') {
            $existing = $this->findBy('mobile', $mobile);
            if ($existing) {
                return $existing;
            }
        }

        if (empty($data['customer_code'])) {
            $data['customer_code'] = $this->generateCustomerCode();
        }
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
