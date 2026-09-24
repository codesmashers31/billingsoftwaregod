<?php
/**
 * Application Configuration
 */

// Load .env variables
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1], " \t\n\r\0\x0B\"'");
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $val;
                putenv("$key=$val");
            }
        }
    }
}

return [
    'app_name' => getenv('APP_NAME') ?: 'Divya Murti ERP',
    'app_env' => getenv('APP_ENV') ?: 'local',
    'app_url' => rtrim(getenv('APP_URL') ?: 'http://localhost/projectgod', '/'),
    'debug' => filter_var(getenv('APP_DEBUG') ?: true, FILTER_VALIDATE_BOOLEAN),
    'timezone' => getenv('TIMEZONE') ?: 'Asia/Kolkata',
    'session_lifetime' => 86400, // 24 hours
    'upload_dir' => dirname(__DIR__) . '/public/assets/uploads/',
    'storage_dir' => dirname(__DIR__) . '/storage/',
];
