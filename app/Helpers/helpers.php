<?php
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

if (!function_exists('url')) {
    function url(string $path = ''): string {
        static $baseUrl = null;
        if ($baseUrl === null) {
            $config = require dirname(__DIR__, 2) . '/config/config.php';
            $baseUrl = rtrim($config['app_url'], '/');
        }
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return Session::getCsrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed {
        return Session::get('_old_input_' . $key, $default);
    }
}

if (!function_exists('auth')) {
    function auth(?string $key = null, mixed $default = null): mixed {
        $user = Auth::user();
        if ($key === null) {
            return $user;
        }
        return $user[$key] ?? $default;
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission(string $permission): bool {
        return Auth::hasPermission($permission);
    }
}

if (!function_exists('hasRole')) {
    function hasRole(string|array $role): bool {
        return Auth::hasRole($role);
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency(float|int|string|null $amount): string {
        $val = (float)($amount ?? 0);
        return '₹' . number_format($val, 2, '.', ',');
    }
}

if (!function_exists('formatDate')) {
    function formatDate(?string $date, string $format = 'd M Y'): string {
        if (!$date) return 'N/A';
        return date($format, strtotime($date));
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime(?string $datetime, string $format = 'd M Y, h:i A'): string {
        if (!$datetime) return 'N/A';
        return date($format, strtotime($datetime));
    }
}

if (!function_exists('sanitize')) {
    function sanitize(mixed $val): string {
        if (is_array($val)) {
            return '';
        }
        return htmlspecialchars(trim((string)($val ?? '')), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('escapeHtml')) {
    function escapeHtml(mixed $val): string {
        if (is_array($val)) {
            return '';
        }
        return htmlspecialchars(addslashes(trim((string)($val ?? ''))), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('getSetting')) {
    function getSetting(string $key, mixed $default = ''): mixed {
        static $settingsCache = null;
        if ($settingsCache === null) {
            $settingsCache = [];
            try {
                $db = Database::getInstance();
                $rows = $db->query("SELECT key_name, value FROM `settings`");
                if (is_array($rows)) {
                    foreach ($rows as $r) {
                        $settingsCache[$r['key_name']] = $r['value'];
                    }
                }
            } catch (\Throwable $e) {
                // Return default on error
            }
        }
        return $settingsCache[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $val = null): mixed {
        return Session::flash($key, $val);
    }
}

if (!function_exists('isActiveRoute')) {
    function isActiveRoute(string $prefix, ?string $currentUri = null): bool {
        if ($currentUri === null) {
            $currentUri = (new \App\Core\Request())->uri();
        }
        return str_starts_with($currentUri, $prefix);
    }
}
