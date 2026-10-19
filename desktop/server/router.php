<?php
/**
 * PHP Built-in Server Router for Standalone Desktop Mode
 * Dispatches static assets directly and routes all application requests to public/index.php
 */

declare(strict_types=1);

// Resolve public directory dynamically (supports packaged app and dev environment)
$publicDir = getenv('PUBLIC_DIR') ?: (
    is_dir(__DIR__ . '/../public') ? realpath(__DIR__ . '/../public') : realpath(dirname(__DIR__, 2) . '/public')
);

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$decodedPath = urldecode($parsedPath);

$targetFile = $publicDir . $decodedPath;

// Serve user uploads from persistent userData storage if applicable
$uploadDir = getenv('UPLOAD_DIR');
if ($uploadDir && is_dir($uploadDir)) {
    if (str_starts_with($decodedPath, '/assets/uploads/')) {
        $sub = substr($decodedPath, strlen('/assets/uploads/'));
        $cand = rtrim($uploadDir, '/\\') . '/' . ltrim($sub, '/\\');
        if (file_exists($cand) && is_file($cand)) {
            $targetFile = $cand;
        }
    } elseif (str_starts_with($decodedPath, '/uploads/')) {
        $sub = substr($decodedPath, strlen('/uploads/'));
        $cand = rtrim($uploadDir, '/\\') . '/' . ltrim($sub, '/\\');
        if (file_exists($cand) && is_file($cand)) {
            $targetFile = $cand;
        }
    }
}

// 1. Serve static files directly if they exist
if ($decodedPath !== '/' && file_exists($targetFile) && is_file($targetFile)) {
    // Provide correct mime types for common desktop assets
    $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $mimes = [
        'css'  => 'text/css; charset=utf-8',
        'js'   => 'application/javascript; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'pdf'  => 'application/pdf',
    ];

    if (isset($mimes[$ext])) {
        header("Content-Type: {$mimes[$ext]}");
    }

    return false; // Built-in web server handles reading and streaming the file
}

// 2. Dynamic Route Handling
$_GET['url'] = ltrim($decodedPath, '/');
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $publicDir . '/index.php';

chdir($publicDir);
require $publicDir . '/index.php';
