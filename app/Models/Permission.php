<?php
namespace App\Models;

use App\Core\Model;

class Permission extends Model {
    protected string $table = 'permissions';
    protected array $fillable = ['module', 'name', 'slug', 'description'];

    public function getAllGroupedByModule(): array {
        $all = $this->all('module ASC, id ASC');
        $grouped = [];
        foreach ($all as $perm) {
            $grouped[$perm['module']][] = $perm;
        }
        return $grouped;
    }
}
