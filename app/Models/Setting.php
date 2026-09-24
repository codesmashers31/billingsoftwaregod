<?php
namespace App\Models;

use App\Core\Model;

class Setting extends Model {
    protected string $table = 'settings';
    protected array $fillable = ['setting_group', 'key_name', 'value', 'description'];

    public function getAllGrouped(): array {
        $all = $this->all('setting_group ASC, id ASC');
        $grouped = [];
        foreach ($all as $s) {
            $grouped[$s['setting_group']][] = $s;
        }
        return $grouped;
    }

    public function set(string $key, mixed $value, string $group = 'general', ?string $desc = null): void {
        $existing = $this->findBy('key_name', $key);
        if ($existing) {
            $this->update($existing['id'], ['value' => (string)$value]);
        } else {
            $this->create([
                'setting_group' => $group,
                'key_name' => $key,
                'value' => (string)$value,
                'description' => $desc
            ]);
        }
    }
}
