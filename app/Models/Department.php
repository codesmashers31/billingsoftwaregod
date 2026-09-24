<?php
namespace App\Models;

use App\Core\Model;

class Department extends Model {
    protected string $table = 'departments';
    protected array $fillable = ['name', 'code', 'description', 'status'];

    public function getWithUserCounts(): array {
        $sql = "SELECT d.*, COUNT(u.id) as user_count 
                FROM `departments` d 
                LEFT JOIN `users` u ON d.id = u.department_id 
                GROUP BY d.id 
                ORDER BY d.id ASC";
        return $this->db->query($sql);
    }
}
