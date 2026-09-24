<?php
namespace App\Core;

use Throwable;

class App {
    public function run(): void {
        // Register Autoloader for App\ namespace
        spl_autoload_register(function ($class) {
            $prefix = 'App\\';
            $baseDir = dirname(__DIR__) . '/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });

        // Require Global Helpers
        require_once dirname(__DIR__) . '/Helpers/helpers.php';

        // Load Config & Set Timezone
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        date_default_timezone_set($config['timezone'] ?? 'Asia/Kolkata');

        // Start Session
        Session::start();

        // Check for "Remember Me" cookie if not logged in
        if (!Auth::check() && isset($_COOKIE['god_remember'])) {
            $token = $_COOKIE['god_remember'];
            $db = Database::getInstance();
            $user = $db->query("SELECT * FROM `users` WHERE remember_token = ? AND status = 'active' LIMIT 1", [$token]);
            if (!empty($user)) {
                Auth::login($user[0], true);
            }
        }

        // Global Error / Exception Handler
        set_exception_handler(function (Throwable $e) use ($config) {
            error_log("Unhandled Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            $req = new Request();
            if ($req->isAjax()) {
                Response::error($config['debug'] ? $e->getMessage() : 'A server error occurred.', [
                    'file' => $config['debug'] ? $e->getFile() : null,
                    'line' => $config['debug'] ? $e->getLine() : null,
                ], 500);
            } else {
                Response::setStatus(500);
                $viewFile = dirname(__DIR__) . '/Views/errors/500.php';
                if (file_exists($viewFile)) {
                    $error = $e;
                    require $viewFile;
                } else {
                    echo "<h1>500 Internal Server Error</h1><p>" . ($config['debug'] ? htmlspecialchars($e->getMessage()) : 'An internal error occurred.') . "</p>";
                }
                exit;
            }
        });

        // Load Routes
        require_once dirname(__DIR__, 2) . '/routes/web.php';

        // Dispatch
        $request = new Request();
        Router::dispatch($request);
    }
}
