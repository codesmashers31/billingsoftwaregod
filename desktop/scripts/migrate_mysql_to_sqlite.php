<?php
/**
 * Safe, Non-Destructive MySQL to SQLite Migration Script
 * Reads all 29 tables from MySQL `god_statue_erp` and generates `desktop/database/billing.sqlite`
 */

declare(strict_types=1);

$mysqlHost = '127.0.0.1';
$mysqlPort = 3306;
$mysqlDb   = 'god_statue_erp';
$mysqlUser = 'root';
$mysqlPass = '';

$sqliteFile = dirname(__DIR__) . '/database/billing.sqlite';

echo "=== MIGRATING MYSQL -> SQLITE ===\n";
echo "Target SQLite File: {$sqliteFile}\n";

if (file_exists($sqliteFile)) {
    unlink($sqliteFile);
}

try {
    $mysql = new PDO(
        "mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlDb};charset=utf8mb4",
        $mysqlUser,
        $mysqlPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $sqlite = new PDO(
        "sqlite:{$sqliteFile}",
        null,
        null,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $sqlite->exec("PRAGMA journal_mode = WAL;");
    $sqlite->exec("PRAGMA synchronous = NORMAL;");
    $sqlite->exec("PRAGMA foreign_keys = OFF;"); // Temporarily off during batch import

    // Tables in safe insertion order
    $tables = [
        'departments',
        'roles',
        'permissions',
        'role_permissions',
        'users',
        'user_permissions',
        'settings',
        'accounts',
        'categories',
        'subcategories',
        'suppliers',
        'supplier_payments',
        'supplier_payment_history',
        'products',
        'product_images',
        'inventory_transactions',
        'customers',
        'invoices',
        'invoice_items',
        'payments',
        'purchases',
        'purchase_items',
        'sales_returns',
        'sales_return_items',
        'expenses',
        'expense_categories',
        'account_transactions',
        'activity_logs',
        'password_resets'
    ];

    $totalRowsCopied = 0;

    foreach ($tables as $table) {
        // 1. Fetch column info from MySQL
        $colStmt = $mysql->query("SHOW COLUMNS FROM `{$table}`");
        $cols = $colStmt->fetchAll();

        $colDefs = [];
        $colNames = [];

        foreach ($cols as $col) {
            $field = $col['Field'];
            $type = strtolower($col['Type']);
            $null = $col['Null'] === 'YES' ? '' : 'NOT NULL';
            $default = $col['Default'];
            $extra = strtolower($col['Extra']);

            $colNames[] = $field;

            // Map MySQL type to SQLite type
            if ($col['Key'] === 'PRI' && str_contains($extra, 'auto_increment')) {
                $sqliteType = 'INTEGER PRIMARY KEY AUTOINCREMENT';
                $colDefs[] = "`{$field}` {$sqliteType}";
                continue;
            }

            if (str_contains($type, 'int')) {
                $sqliteType = 'INTEGER';
            } elseif (str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
                $sqliteType = 'NUMERIC';
            } else {
                $sqliteType = 'TEXT';
            }

            $defClause = '';
            if ($default !== null) {
                if (is_numeric($default)) {
                    $defClause = "DEFAULT {$default}";
                } elseif (str_contains(strtoupper($default), 'CURRENT_TIMESTAMP')) {
                    $defClause = "DEFAULT CURRENT_TIMESTAMP";
                } else {
                    $defClause = "DEFAULT " . $sqlite->quote($default);
                }
            }

            $parts = array_filter(["`{$field}`", $sqliteType, $null, $defClause]);
            $colDefs[] = implode(' ', $parts);
        }

        // Add unique constraints where applicable
        if ($table === 'supplier_payments') {
            $colDefs[] = "UNIQUE(`supplier_id`)";
        }

        $createSql = "CREATE TABLE IF NOT EXISTS `{$table}` (\n  " . implode(",\n  ", $colDefs) . "\n);";
        $sqlite->exec($createSql);

        // 2. Copy data
        $dataStmt = $mysql->query("SELECT * FROM `{$table}`");
        $rows = $dataStmt->fetchAll();
        $rowCount = count($rows);

        if ($rowCount > 0) {
            $placeholders = implode(', ', array_fill(0, count($colNames), '?'));
            $colList = implode('`, `', $colNames);
            $insertSql = "INSERT INTO `{$table}` (`{$colList}`) VALUES ({$placeholders})";
            $insertStmt = $sqlite->prepare($insertSql);

            $sqlite->beginTransaction();
            foreach ($rows as $row) {
                $vals = [];
                foreach ($colNames as $c) {
                    $vals[] = $row[$c];
                }
                $insertStmt->execute($vals);
            }
            $sqlite->commit();
        }

        $totalRowsCopied += $rowCount;
        printf(" -> Table %-26s : %4d rows copied\n", $table, $rowCount);
    }

    $sqlite->exec("PRAGMA foreign_keys = ON;");

    echo "===================================\n";
    echo "SUCCESS: All 29 tables migrated to SQLite!\n";
    echo "Total Rows Migrated: {$totalRowsCopied}\n";
    echo "SQLite File Size: " . number_format(filesize($sqliteFile) / 1024, 2) . " KB\n";

} catch (Exception $e) {
    echo "MIGRATION ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
