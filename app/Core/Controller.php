<?php
namespace App\Core;

abstract class Controller {
    protected function render(string $viewPath, array $data = [], ?string $layout = 'app'): void {
        extract($data);
        $contentView = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($contentView)) {
            die("View file not found: {$contentView}");
        }

        if ($layout === null) {
            require $contentView;
            return;
        }

        $layoutFile = dirname(__DIR__) . '/Views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            die("Layout file not found: {$layoutFile}");
        }

        // Start output buffering for content view
        ob_start();
        require $contentView;
        $content = ob_get_clean();

        // Render through main layout
        require $layoutFile;
    }

    protected function json(array $data, int $status = 200): void {
        Response::json($data, $status);
    }

    protected function success(string $message = 'Success', array $data = [], int $status = 200): void {
        Response::success($message, $data, $status);
    }

    protected function error(string $message = 'Error', array $errors = [], int $status = 400): void {
        Response::error($message, $errors, $status);
    }

    protected function redirect(string $path): void {
        Response::redirect($path);
    }

    protected function requirePermission(string $permissionSlug): void {
        Auth::requirePermission($permissionSlug);
    }

    protected function logActivity(string $action, string $module, ?int $refId = null, ?string $refCode = null, string $description = ''): void {
        Auth::logActivity($action, $module, $refId, $refCode, $description);
    }

    protected function validate(array $data, array $rules): array {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $val = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && ($val === null || $val === '')) {
                    $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                } elseif ($rule === 'email' && !empty($val) && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Invalid email address.';
                } elseif ($rule === 'numeric' && !empty($val) && !is_numeric($val)) {
                    $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number.';
                } elseif (str_starts_with($rule, 'min:') && !empty($val)) {
                    $min = (int)substr($rule, 4);
                    if (strlen((string)$val) < $min) {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
                    }
                }
            }
        }
        return $errors;
    }
}
