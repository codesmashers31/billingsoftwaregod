<?php
namespace App\Models;

use App\Core\Model;

class Invoice extends Model {
    protected string $table = 'invoices';
    protected array $fillable = [
        'invoice_no', 'invoice_type', 'reference_no', 'customer_id', 'user_id', 
        'invoice_date', 'due_date', 'invoice_time', 'payment_terms',
        'subtotal', 'item_discount_total', 'overall_discount_percent', 'overall_discount_amount',
        'cgst_amount', 'sgst_amount', 'igst_amount', 'tax_amount', 'additional_charges', 'round_off',
        'grand_total', 'paid_amount', 'due_amount', 'payment_method', 'status',
        'payment_status', 'notes'
    ];

    public function getWithDetails(int $id): ?array {
        $sql = "SELECT i.*, 
                       c.name as customer_name, c.mobile as customer_mobile, c.email as customer_email, 
                       c.address as customer_address, c.shipping_address as customer_shipping_address,
                       c.city as customer_city, c.state as customer_state, c.state_code as customer_state_code,
                       c.gst_number as customer_gst, c.customer_type, u.name as cashier_name 
                FROM `invoices` i 
                JOIN `customers` c ON i.customer_id = c.id 
                JOIN `users` u ON i.user_id = u.id 
                WHERE i.id = ? LIMIT 1";
        $res = $this->db->query($sql, [$id]);
        return !empty($res) ? $res[0] : null;
    }

    public function getItems(int $invoiceId): array {
        $sql = "SELECT ii.*, p.image, p.current_stock, p.sku, c.name as category_name
                FROM `invoice_items` ii 
                LEFT JOIN `products` p ON ii.product_id = p.id 
                LEFT JOIN `categories` c ON p.category_id = c.id
                WHERE ii.invoice_id = ?";
        return $this->db->query($sql, [$invoiceId]);
    }

    public function generateInvoiceNo(string $prefix = ''): string {
        $year = date('Y');
        if (empty($prefix)) {
            $prefix = "INV-{$year}-";
        }
        if (!str_ends_with($prefix, '-')) {
            $prefix .= '-';
        }

        $sql = "SELECT invoice_no FROM `invoices` WHERE invoice_no LIKE ? ORDER BY id DESC LIMIT 1";
        $last = $this->db->query($sql, [$prefix . '%']);
        
        if (!empty($last)) {
            $numPart = preg_replace('/[^0-9]/', '', substr($last[0]['invoice_no'], strlen($prefix)));
            $nextNum = max(1, (int)$numPart + 1);
        } else {
            $nextNum = 1;
        }

        $candidate = $prefix . str_pad((string)$nextNum, 4, '0', STR_PAD_LEFT);
        while ($this->findBy('invoice_no', $candidate)) {
            $nextNum++;
            $candidate = $prefix . str_pad((string)$nextNum, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    public function getPaginatedList(int $page = 1, int $perPage = 15, array $filters = []): array {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $term = "%{$filters['search']}%";
            $conditions[] = "(i.invoice_no LIKE ? OR c.name LIKE ? OR c.mobile LIKE ? OR i.reference_no LIKE ?)";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        if (!empty($filters['status'])) {
            $conditions[] = "i.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['invoice_type'])) {
            $conditions[] = "i.invoice_type = ?";
            $params[] = $filters['invoice_type'];
        }

        if (!empty($filters['payment_status'])) {
            if ($filters['payment_status'] === 'overdue') {
                $conditions[] = "i.status != 'cancelled' AND i.due_amount > 0 AND i.due_date IS NOT NULL AND i.due_date < CURDATE()";
            } else {
                $conditions[] = "i.payment_status = ?";
                $params[] = $filters['payment_status'];
            }
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

        $dataSql = "SELECT i.*, 
                           c.name as customer_name, c.mobile as customer_mobile, c.email as customer_email,
                           c.state as customer_state, u.name as cashier_name,
                           CASE 
                               WHEN i.status != 'cancelled' AND i.due_amount > 0 AND i.due_date IS NOT NULL AND i.due_date < CURDATE() THEN 1 
                               ELSE 0 
                           END as is_overdue
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

    public function cancel(int $id, string $reason = '', int $userId = 1): bool {
        $invoice = $this->find($id);
        if (!$invoice || $invoice['status'] === 'cancelled') {
            return false;
        }

        $items = $this->getItems($id);
        $invTxModel = new Inventory();

        if ($invoice['status'] === 'completed') {
            foreach ($items as $item) {
                $invTxModel->adjustStock(
                    (int)$item['product_id'],
                    (int)$item['quantity'],
                    'stock_in',
                    'invoice_cancellation',
                    $id,
                    $invoice['invoice_no'],
                    $reason ?: "Restocked on Invoice {$invoice['invoice_no']} cancellation",
                    $userId
                );
            }

            if ((float)$invoice['due_amount'] > 0) {
                $custModel = new Customer();
                $cust = $custModel->find((int)$invoice['customer_id']);
                if ($cust) {
                    $newCustBal = max(0, (float)$cust['outstanding_balance'] - (float)$invoice['due_amount']);
                    $custModel->update((int)$cust['id'], ['outstanding_balance' => $newCustBal]);
                }
            }
        }

        return (bool)$this->update($id, ['status' => 'cancelled']);
    }
}
