<?php
/**
 * Entry point for God Statue ERP
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Core/App.php';

$app = new \App\Core\App();
$app->run();
