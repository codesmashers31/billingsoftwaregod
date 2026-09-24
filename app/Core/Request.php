<?php
namespace App\Core;

class Request {
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private array $json;

    public function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        $rawInput = file_get_contents('php://input');
        $this->json = json_decode($rawInput, true) ?? [];
    }

    public function method(): string {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool {
        return $this->method() === 'POST';
    }

    public function isGet(): bool {
        return $this->method() === 'GET';
    }

    public function isAjax(): bool {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) && 
                strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               str_contains($this->server['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    public function input(string $key, mixed $default = null): mixed {
        if (isset($this->json[$key])) {
            return $this->json[$key];
        }
        if (isset($this->post[$key])) {
            return is_string($this->post[$key]) ? trim($this->post[$key]) : $this->post[$key];
        }
        if (isset($this->get[$key])) {
            return is_string($this->get[$key]) ? trim($this->get[$key]) : $this->get[$key];
        }
        return $default;
    }

    public function all(): array {
        return array_merge($this->get, $this->post, $this->json);
    }

    public function file(string $key): ?array {
        return $this->files[$key] ?? null;
    }

    public function ip(): string {
        return $this->server['HTTP_X_FORWARDED_FOR'] ?? $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function uri(): string {
        if (!empty($this->get['url'])) {
            return '/' . trim($this->get['url'], '/');
        }

        $uri = $this->server['REQUEST_URI'] ?? '/';
        
        // Remove query string
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }

        // Normalize slashes for Windows Apache
        $uri = str_replace('\\', '/', $uri);
        $scriptDir = str_replace('\\', '/', dirname($this->server['SCRIPT_NAME'] ?? ''));
        
        // 1. If URI starts with script directory (e.g. /projectgod/public)
        if ($scriptDir !== '/' && $scriptDir !== '.' && !empty($scriptDir) && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }
        
        // 2. If URI starts with parent directory of script (e.g. /projectgod)
        $parentDir = str_replace('\\', '/', dirname($scriptDir));
        if ($parentDir !== '/' && $parentDir !== '.' && !empty($parentDir) && str_starts_with($uri, $parentDir)) {
            $uri = substr($uri, strlen($parentDir));
        }

        // Remove index.php if present
        if (str_starts_with($uri, '/index.php')) {
            $uri = substr($uri, strlen('/index.php'));
        }

        $clean = '/' . trim($uri, '/');
        return $clean === '//' ? '/' : $clean;
    }

    public function validateCsrf(): bool {
        $token = $this->input('_csrf_token') ?? 
                 ($this->server['HTTP_X_CSRF_TOKEN'] ?? null);
        return Session::validateCsrfToken($token);
    }
}
