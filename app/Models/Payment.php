<?php
namespace App\Models;

use App\Core\Model;

class Payment extends Model {
    protected string $table = 'payments';
    protected array $fillable = [
        'payment_no', 'payment_type', 'account_id', 'customer_id', 
        'supplier_id', 'invoice_id', 'purchase_id', 'amount', 
        'payment_method', 'transaction_reference', 'payment_date', 'notes', 'user_id'
    ];

    public function generatePaymentNo(): string {
        $prefix = 'PAY-' . date('Ym') . '-';
        $sql = "SELECT payment_no FROM `payments` WHERE payment_no LIKE ? ORDER BY id DESC LIMIT 1";
        $last = $this->db->query($sql, [$prefix . '%']);
        if (!empty($last)) {
            $num = (int)substr($last[0]['payment_no'], strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    public function getPaginatedList(int $page = 1, int $perPage = 15, array $filters = []): array {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['payment_type'])) {
            $conditions[] = "p.payment_type = ?";
            $params[] = $filters['payment_type'];
        }

        if (!empty($filters['account_id'])) {
            $conditions[] = "p.account_id = ?";
            $params[] = (int)$filters['account_id'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "p.payment_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "p.payment_date <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $conditions);
        $offset = max(0, ($page - 1) * $perPage);

        $countSql = "SELECT COUNT(*) as total FROM `payments` p WHERE {$whereClause}";
        $totalRes = $this->db->query($countSql, $params);
        $total = isset($totalRes[0]['total']) ? (int)$totalRes[0]['total'] : 0;

        $dataSql = "SELECT p.*, a.name as account_name, c.name as customer_name, s.name as supplier_name, u.name as cashier_name 
                    FROM `payments` p 
                    LEFT JOIN `accounts` a ON p.account_id = a.id 
                    LEFT JOIN `customers` c ON p.customer_id = c.id 
                    LEFT JOIN `suppliers` s ON p.supplier_id = s.id 
                    LEFT JOIN `users` u ON p.user_id = u.id 
                    WHERE {$whereClause} 
                    ORDER BY p.id DESC 
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
}
