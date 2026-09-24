<?php
namespace App\Models;

use App\Core\Model;

class Role extends Model {
    protected string $table = 'roles';
    protected array $fillable = ['name', 'slug', 'description', 'is_system', 'status'];

    public function getPermissions(int $roleId): array {
        $sql = "SELECT p.* FROM `permissions` p 
                JOIN `role_permissions` rp ON p.id = rp.permission_id 
                WHERE rp.role_id = ?";
        return $this->db->query($sql, [$roleId]);
    }

    public function syncPermissions(int $roleId, array $permissionIds): void {
        $this->db->query("DELETE FROM `role_permissions` WHERE role_id = ?", [$roleId]);
        foreach ($permissionIds as $permId) {
            $permId = (int)$permId;
            if ($permId > 0) {
                $this->db->query("INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES (?, ?)", [$roleId, $permId]);
            }
        }
    }
}
