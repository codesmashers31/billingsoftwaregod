<?php
namespace App\Models;

use App\Core\Model;

class Expense extends Model {
    protected string $table = 'expenses';
    protected array $fillable = [
        'expense_no', 'category_id', 'account_id', 'title', 'amount', 
        'payment_method', 'expense_date', 'description', 'attachment', 
        'approved_by', 'user_id', 'status'
    ];

    public function generateExpenseNo(): string {
        $prefix = 'EXP-' . date('Ym') . '-';
        $sql = "SELECT expense_no FROM `expenses` WHERE expense_no LIKE ? ORDER BY id DESC LIMIT 1";
        $last = $this->db->query($sql, [$prefix . '%']);
        if (!empty($last)) {
            $num = (int)substr($last[0]['expense_no'], strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    public function getPaginatedList(int $page = 1, int $perPage = 15, array $filters = []): array {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $term = "%{$filters['search']}%";
            $conditions[] = "(e.title LIKE ? OR e.expense_no LIKE ?)";
            $params = array_merge($params, [$term, $term]);
        }

        if (!empty($filters['category_id'])) {
            $conditions[] = "e.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = "e.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "e.expense_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "e.expense_date <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $conditions);
        $offset = max(0, ($page - 1) * $perPage);

        $countSql = "SELECT COUNT(*) as total FROM `expenses` e WHERE {$whereClause}";
        $totalRes = $this->db->query($countSql, $params);
        $total = isset($totalRes[0]['total']) ? (int)$totalRes[0]['total'] : 0;

        $dataSql = "SELECT e.*, ec.name as category_name, a.name as account_name, u.name as creator_name, ap.name as approver_name 
                    FROM `expenses` e 
                    JOIN `expense_categories` ec ON e.category_id = ec.id 
                    JOIN `accounts` a ON e.account_id = a.id 
                    JOIN `users` u ON e.user_id = u.id 
                    LEFT JOIN `users` ap ON e.approved_by = ap.id 
                    WHERE {$whereClause} 
                    ORDER BY e.expense_date DESC, e.id DESC 
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
