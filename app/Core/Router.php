<?php
namespace App\Core;

use Exception;

class Router {
    private static array $routes = [];

    public static function get(string $path, array|callable $handler, array $middlewares = []): void {
        self::addRoute('GET', $path, $handler, $middlewares);
    }

    public static function post(string $path, array|callable $handler, array $middlewares = []): void {
        self::addRoute('POST', $path, $handler, $middlewares);
    }

    public static function any(string $path, array|callable $handler, array $middlewares = []): void {
        self::addRoute('GET', $path, $handler, $middlewares);
        self::addRoute('POST', $path, $handler, $middlewares);
    }

    private static function addRoute(string $method, string $path, array|callable $handler, array $middlewares): void {
        $path = '/' . trim($path, '/');
        // Convert {param} placeholders to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = "#^" . $pattern . "$#";

        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public static function dispatch(Request $request): void {
        $method = $request->method();
        $uri = $request->uri();

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                // Execute middlewares if any
                foreach ($route['middlewares'] as $middleware) {
                    if (is_callable($middleware)) {
                        $middleware($request);
                    }
                }

                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func($handler, $request, $params);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $action] = $handler;
                    if (!class_exists($controllerClass)) {
                        throw new Exception("Controller class not found: {$controllerClass}");
                    }
                    $controller = new $controllerClass();
                    if (!method_exists($controller, $action)) {
                        throw new Exception("Action {$action} not found on controller {$controllerClass}");
                    }
                    $controller->$action($request, $params);
                    return;
                }
            }
        }

        // No route matched: Render 404
        Response::setStatus(404);
        if ($request->isAjax()) {
            Response::error("Resource not found: {$uri}", [], 404);
        } else {
            $viewFile = dirname(__DIR__) . '/Views/errors/404.php';
            if (file_exists($viewFile)) {
                require $viewFile;
            } else {
                echo "<h1>404 - Page Not Found</h1><p>The requested URL {$uri} was not found.</p>";
            }
            exit;
        }
    }
}
