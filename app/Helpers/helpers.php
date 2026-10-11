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

if (!function_exists('amountToWords')) {
    function amountToWords(float $number): string {
        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
            30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
            80 => 'Eighty', 90 => 'Ninety'
        ];
        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        
        $str = [];
        $no_str = (string)$no;
        $length = strlen($no_str);
        
        if ($no == 0) {
            $result = 'Zero Rupees';
        } else {
            $num_parts = [];
            if ($length > 2) {
                $num_parts[] = substr($no_str, -2);
                $no_str = substr($no_str, 0, -2);
                if (strlen($no_str) > 0) {
                    $num_parts[] = substr($no_str, -1);
                    $no_str = substr($no_str, 0, -1);
                }
                while (strlen($no_str) > 0) {
                    $num_parts[] = substr($no_str, -2);
                    $no_str = substr($no_str, 0, -2);
                }
            } else {
                $num_parts[] = $no_str;
            }

            $part_count = count($num_parts);
            for ($i = $part_count - 1; $i >= 0; $i--) {
                $val = (int)$num_parts[$i];
                if ($val == 0) continue;
                $part_words = '';
                if ($i == 1) { // Hundreds place
                    $part_words = ($words[$val] ?? '') . ' Hundred';
                } elseif ($val < 20) {
                    $part_words = $words[$val] ?? '';
                } else {
                    $tens = (int)($val / 10) * 10;
                    $units = $val % 10;
                    $part_words = ($words[$tens] ?? '') . ($units ? ' ' . ($words[$units] ?? '') : '');
                }
                if ($i > 1) {
                    $unit_name = $digits[$i] ?? '';
                    $str[] = trim($part_words . ' ' . $unit_name);
                } else {
                    $str[] = trim($part_words);
                }
            }
            $result = implode(' ', array_filter($str)) . ' Rupees';
        }

        if ($point > 0) {
            $p_val = (int)$point;
            if ($p_val < 20) {
                $p_word = $words[$p_val] ?? '';
            } else {
                $p_tens = (int)($p_val / 10) * 10;
                $p_units = $p_val % 10;
                $p_word = ($words[$p_tens] ?? '') . ($p_units ? ' ' . ($words[$p_units] ?? '') : '');
            }
            $result .= ' and ' . $p_word . ' Paise';
        }

        return $result . ' Only';
    }
}
