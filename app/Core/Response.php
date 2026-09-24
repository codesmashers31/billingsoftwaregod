<?php
namespace App\Core;

class Response {
    public static function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success(string $message = 'Success', array $data = [], int $status = 200): void {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(string $message = 'Error', array $errors = [], int $status = 400): void {
        self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public static function redirect(string $path): void {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $baseUrl = $config['app_url'];
        $url = str_starts_with($path, 'http') ? $path : $baseUrl . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }

    public static function setStatus(int $code): void {
        http_response_code($code);
    }
}
