<?php
namespace App\Core;

class Auth {
    private static ?array $cachedUser = null;
    private static ?array $cachedPermissions = null;

    public static function check(): bool {
        Session::start();
        return Session::has('user_id');
    }

    public static function id(): ?int {
        Session::start();
        return Session::get('user_id');
    }

    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }

        if (self::$cachedUser === null) {
            $db = Database::getInstance();
            $userId = self::id();
            $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug, d.name as department_name 
                    FROM `users` u 
                    LEFT JOIN `roles` r ON u.role_id = r.id 
                    LEFT JOIN `departments` d ON u.department_id = d.id 
                    WHERE u.id = ? AND u.status = 'active' LIMIT 1";
            $res = $db->query($sql, [$userId]);
            if (!empty($res)) {
                self::$cachedUser = $res[0];
            } else {
                self::logout();
                return null;
            }
        }

        return self::$cachedUser;
    }

    public static function permissions(): array {
        if (self::$cachedPermissions !== null) {
            return self::$cachedPermissions;
        }

        $user = self::user();
        if (!$user) {
            return [];
        }

        // Super admin has all permissions
        if ($user['role_slug'] === 'super-admin') {
            $db = Database::getInstance();
            $allPerms = $db->query("SELECT slug FROM `permissions`");
            self::$cachedPermissions = array_column($allPerms, 'slug');
            return self::$cachedPermissions;
        }

        $db = Database::getInstance();
        // Role permissions
        $sqlRole = "SELECT p.slug FROM `permissions` p 
                    JOIN `role_permissions` rp ON p.id = rp.permission_id 
                    WHERE rp.role_id = ?";
        $rolePerms = $db->query($sqlRole, [$user['role_id']]);
        $perms = array_column($rolePerms, 'slug');

        // Custom User permission overrides
        $sqlUser = "SELECT p.slug, up.is_granted FROM `permissions` p 
                    JOIN `user_permissions` up ON p.id = up.permission_id 
                    WHERE up.user_id = ?";
        $userOverrides = $db->query($sqlUser, [$user['id']]);

        foreach ($userOverrides as $override) {
            if ($override['is_granted']) {
                if (!in_array($override['slug'], $perms, true)) {
                    $perms[] = $override['slug'];
                }
            } else {
                $perms = array_diff($perms, [$override['slug']]);
            }
        }

        self::$cachedPermissions = array_values($perms);
        return self::$cachedPermissions;
    }

    public static function hasPermission(string $permissionSlug): bool {
        $user = self::user();
        if (!$user) {
            return false;
        }
        if ($user['role_slug'] === 'super-admin') {
            return true;
        }
        return in_array($permissionSlug, self::permissions(), true);
    }

    public static function hasRole(string|array $roles): bool {
        $user = self::user();
        if (!$user) {
            return false;
        }
        if ($user['role_slug'] === 'super-admin') {
            return true;
        }
        $roles = (array)$roles;
        return in_array($user['role_slug'], $roles, true);
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            Session::flash('error', 'Please log in to continue.');
            Response::redirect('/login');
        }
    }

    public static function requirePermission(string $permissionSlug): void {
        self::requireAuth();
        if (!self::hasPermission($permissionSlug)) {
            $req = new Request();
            if ($req->isAjax()) {
                Response::error("Unauthorized. You do not have permission: {$permissionSlug}", [], 403);
            } else {
                Response::setStatus(403);
                $viewFile = dirname(__DIR__) . '/Views/errors/403.php';
                if (file_exists($viewFile)) {
                    require $viewFile;
                } else {
                    echo "<h1>403 Forbidden</h1><p>You do not have permission to access this resource.</p>";
                }
                exit;
            }
        }
    }

    public static function login(array $user, bool $remember = false): void {
        Session::start();
        session_regenerate_id(true);
        Session::set('user_id', (int)$user['id']);
        Session::set('username', $user['username']);
        Session::set('user_name', $user['name']);
        Session::set('role_id', (int)$user['role_id']);

        // Update last login
        $db = Database::getInstance();
        $ip = (new Request())->ip();
        $db->query("UPDATE `users` SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?", [$ip, $user['id']]);

        // Audit log
        self::logActivity('login', 'auth', $user['id'], $user['username'], "User {$user['name']} logged in successfully");

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $db->query("UPDATE `users` SET remember_token = ? WHERE id = ?", [$token, $user['id']]);
            setcookie('god_remember', $token, time() + (86400 * 30), '/', '', false, true);
        }
    }

    public static function logout(): void {
        if (self::check()) {
            $user = self::user();
            if ($user) {
                self::logActivity('logout', 'auth', $user['id'], $user['username'], "User {$user['name']} logged out");
            }
        }
        setcookie('god_remember', '', time() - 3600, '/');
        Session::destroy();
        self::$cachedUser = null;
        self::$cachedPermissions = null;
    }

    public static function logActivity(string $action, string $module, ?int $refId = null, ?string $refCode = null, string $description = ''): void {
        try {
            $db = Database::getInstance();
            $userId = self::id();
            $req = new Request();
            $sql = "INSERT INTO `activity_logs` (`user_id`, `action`, `module`, `reference_id`, `reference_code`, `description`, `ip_address`, `user_agent`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $db->query($sql, [
                $userId,
                $action,
                $module,
                $refId,
                $refCode,
                $description,
                $req->ip(),
                substr($req->userAgent(), 0, 250)
            ]);
        } catch (\Throwable $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }
}
