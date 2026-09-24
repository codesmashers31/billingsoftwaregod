<?php
namespace App\Models;

use App\Core\Model;

class AuditLog extends Model {
    protected string $table = 'activity_logs';
    protected array $fillable = [
        'user_id', 'action', 'module', 'reference_id', 'reference_code', 
        'description', 'ip_address', 'user_agent'
    ];

    public function getPaginatedList(int $page = 1, int $perPage = 25, array $filters = []): array {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['user_id'])) {
            $conditions[] = "al.user_id = ?";
            $params[] = (int)$filters['user_id'];
        }

        if (!empty($filters['module'])) {
            $conditions[] = "al.module = ?";
            $params[] = $filters['module'];
        }

        if (!empty($filters['action'])) {
            $conditions[] = "al.action = ?";
            $params[] = $filters['action'];
        }

        if (!empty($filters['search'])) {
            $term = "%{$filters['search']}%";
            $conditions[] = "(al.description LIKE ? OR al.reference_code LIKE ?)";
            $params[] = array_merge($params, [$term, $term]);
        }

        $whereClause = implode(' AND ', $conditions);
        $offset = max(0, ($page - 1) * $perPage);

        $countSql = "SELECT COUNT(*) as total FROM `activity_logs` al WHERE {$whereClause}";
        $totalRes = $this->db->query($countSql, $params);
        $total = isset($totalRes[0]['total']) ? (int)$totalRes[0]['total'] : 0;

        $dataSql = "SELECT al.*, u.name as user_name, u.username 
                    FROM `activity_logs` al 
                    LEFT JOIN `users` u ON al.user_id = u.id 
                    WHERE {$whereClause} 
                    ORDER BY al.id DESC 
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

    public function getRecentLogs(int $limit = 6): array {
        return $this->db->query("SELECT al.*, u.name as user_name, u.username 
                                 FROM `activity_logs` al 
                                 LEFT JOIN `users` u ON al.user_id = u.id 
                                 ORDER BY al.id DESC LIMIT {$limit}");
    }
}
