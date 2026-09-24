<?php
namespace App\Core;

abstract class Model {
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all(string $orderBy = 'id DESC'): array {
        $sql = "SELECT * FROM `{$this->table}` ORDER BY {$orderBy}";
        return $this->db->query($sql);
    }

    public function find(int $id): ?array {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";
        $result = $this->db->query($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }

    public function findBy(string $column, mixed $value): ?array {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1";
        $result = $this->db->query($sql, [$value]);
        return !empty($result) ? $result[0] : null;
    }

    public function where(string $condition, array $params = [], string $orderBy = 'id DESC', ?int $limit = null): array {
        $sql = "SELECT * FROM `{$this->table}` WHERE {$condition} ORDER BY {$orderBy}";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->db->query($sql, $params);
    }

    public function create(array $data): int|string {
        // Filter by fillable if defined
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $colString = implode('`, `', $columns);
        $placeholderString = implode(', ', $placeholders);

        $sql = "INSERT INTO `{$this->table}` (`{$colString}`) VALUES ({$placeholderString})";
        return $this->db->query($sql, array_values($data));
    }

    public function update(int $id, array $data): int|bool {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        $setPairs = [];
        $values = [];
        foreach ($data as $col => $val) {
            $setPairs[] = "`{$col}` = ?";
            $values[] = $val;
        }
        $values[] = $id;

        $setString = implode(', ', $setPairs);
        $sql = "UPDATE `{$this->table}` SET {$setString} WHERE `{$this->primaryKey}` = ?";
        return $this->db->query($sql, $values);
    }

    public function delete(int $id): int|bool {
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?";
        return $this->db->query($sql, [$id]);
    }

    public function count(string $condition = '1=1', array $params = []): int {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}` WHERE {$condition}";
        $res = $this->db->query($sql, $params);
        return isset($res[0]['total']) ? (int)$res[0]['total'] : 0;
    }

    public function paginate(int $page = 1, int $perPage = 15, string $condition = '1=1', array $params = [], string $orderBy = 'id DESC', string $select = '*'): array {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as total FROM `{$this->table}` WHERE {$condition}";
        $totalResult = $this->db->query($countSql, $params);
        $total = isset($totalResult[0]['total']) ? (int)$totalResult[0]['total'] : 0;

        $dataSql = "SELECT {$select} FROM `{$this->table}` WHERE {$condition} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $rows = $this->db->query($dataSql, $params);

        $totalPages = (int)ceil($total / $perPage);

        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages
        ];
    }

    public function raw(string $sql, array $params = []): array|int|bool {
        return $this->db->query($sql, $params);
    }
}
