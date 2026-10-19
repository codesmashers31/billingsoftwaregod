<?php
/**
 * Database Configuration
 */

return [
    'connection' => getenv('DB_CONNECTION') ?: 'mysql',
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => (int)(getenv('DB_PORT') ?: 3306),
    'database' => getenv('DB_DATABASE') ?: 'god_statue_erp',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'sqlite_path' => getenv('DB_SQLITE_PATH') ?: (dirname(__DIR__) . '/desktop/database/billing.sqlite'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
