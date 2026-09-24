<?php
namespace App\Models;

use App\Core\Model;

class Invoice extends Model {
    protected string $table = 'invoices';
    protected array $fillable = [
        'invoice_no', 'customer_id', 'user_id', 'invoice_date', 'invoice_time',
        'subtotal', 'item_discount_total', 'overall_discount_percent', 'overall_discount_amount',
        'cgst_amount', 'sgst_amount', 'igst_amount', 'tax_amount', 'round_off',
        'grand_total', 'paid_amount', 'due_amount', 'payment_method', 'status',
        'payment_status', 'notes'
    ];

    public function getWithDetails(int $id): ?array {
        $sql = "SELECT i.*, c.name as customer_name, c.mobile as customer_mobile, c.email as customer_email, 
                       c.address as customer_address, c.city as customer_city, c.state as customer_state, 
                       c.gst_number as customer_gst, c.customer_type, u.name as cashier_name 
                FROM `invoices` i 
                JOIN `customers` c ON i.customer_id = c.id 
                JOIN `users` u ON i.user_id = u.id 
                WHERE i.id = ? LIMIT 1";
        $res = $this->db->query($sql, [$id]);
        return !empty($res) ? $res[0] : null;
    }

    public function getItems(int $invoiceId): array {
        $sql = "SELECT ii.*, p.image, p.current_stock, p.sku 
                FROM `invoice_items` ii 
                LEFT JOIN `products` p ON ii.product_id = p.id 
                WHERE ii.invoice_id = ?";
        return $this->db->query($sql, [$invoiceId]);
    }

    public function generateInvoiceNo(): string {
        $prefix = getSetting('invoice_prefix', 'DIVYA-');
        $startNo = (int)getSetting('starting_invoice_no', '1001');

        $sql = "SELECT invoice_no FROM `invoices` WHERE invoice_no LIKE ? ORDER BY id DESC LIMIT 1";
        $last = $this->db->query($sql, [$prefix . '%']);
        
        if (!empty($last)) {
            $numPart = str_replace($prefix, '', $last[0]['invoice_no']);
            $nextNum = max($startNo, (int)$numPart + 1);
        } else {
            $nextNum = $startNo;
        }

        return $prefix . $nextNum;
    }

    public function getPaginatedList(int $page = 1, int $perPage = 15, array $filters = []): array {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $term = "%{$filters['search']}%";
            $conditions[] = "(i.invoice_no LIKE ? OR c.name LIKE ? OR c.mobile LIKE ?)";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($filters['status'])) {
            $conditions[] = "i.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $conditions[] = "i.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "i.invoice_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "i.invoice_date <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $conditions);
        $offset = max(0, ($page - 1) * $perPage);

        $countSql = "SELECT COUNT(*) as total FROM `invoices` i JOIN `customers` c ON i.customer_id = c.id WHERE {$whereClause}";
        $totalRes = $this->db->query($countSql, $params);
        $total = isset($totalRes[0]['total']) ? (int)$totalRes[0]['total'] : 0;

        $dataSql = "SELECT i.*, c.name as customer_name, c.mobile as customer_mobile, u.name as cashier_name 
                    FROM `invoices` i 
                    JOIN `customers` c ON i.customer_id = c.id 
                    JOIN `users` u ON i.user_id = u.id 
                    WHERE {$whereClause} 
                    ORDER BY i.id DESC 
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
