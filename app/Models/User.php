<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected string $table = 'users';
    protected array $fillable = [
        'name', 'username', 'email', 'mobile', 'password', 
        'role_id', 'department_id', 'avatar', 'status', 
        'remember_token', 'last_login_at', 'last_login_ip'
    ];

    public function getWithDetails(int $id): ?array {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug, d.name as department_name 
                FROM `users` u 
                LEFT JOIN `roles` r ON u.role_id = r.id 
                LEFT JOIN `departments` d ON u.department_id = d.id 
                WHERE u.id = ? LIMIT 1";
        $res = $this->db->query($sql, [$id]);
        return !empty($res) ? $res[0] : null;
    }

    public function getPaginatedList(int $page = 1, int $perPage = 15, string $search = '', string $roleFilter = '', string $statusFilter = ''): array {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(u.name LIKE ? OR u.username LIKE ? OR u.email LIKE ? OR u.mobile LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        if (!empty($roleFilter)) {
            $conditions[] = "u.role_id = ?";
            $params[] = (int)$roleFilter;
        }

        if (!empty($statusFilter)) {
            $conditions[] = "u.status = ?";
            $params[] = $statusFilter;
        }

        $whereClause = implode(' AND ', $conditions);
        $offset = max(0, ($page - 1) * $perPage);

        $countSql = "SELECT COUNT(*) as total FROM `users` u WHERE {$whereClause}";
        $totalRes = $this->db->query($countSql, $params);
        $total = isset($totalRes[0]['total']) ? (int)$totalRes[0]['total'] : 0;

        $dataSql = "SELECT u.*, r.name as role_name, d.name as department_name 
                    FROM `users` u 
                    LEFT JOIN `roles` r ON u.role_id = r.id 
                    LEFT JOIN `departments` d ON u.department_id = d.id 
                    WHERE {$whereClause} 
                    ORDER BY u.id DESC 
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
