<?php
/**
 * Convert MySQL Dump (god_statue_erp_backup.sql) into SQLite Database (billing.sqlite)
 */

declare(strict_types=1);

$dumpFile = dirname(__DIR__, 2) . '/god_statue_erp_backup.sql';
$sqliteFile = dirname(__DIR__) . '/database/billing.sqlite';

echo "=== SQL DUMP TO SQLITE CONVERTER ===\n";
echo "Source: {$dumpFile}\n";
echo "Target: {$sqliteFile}\n";

if (!file_exists($dumpFile)) {
    die("Error: SQL dump file not found: {$dumpFile}\n");
}

if (file_exists($sqliteFile)) {
    unlink($sqliteFile);
}

$sqlite = new PDO(
    "sqlite:{$sqliteFile}",
    null,
    null,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sqlite->exec("PRAGMA journal_mode = WAL;");
$sqlite->exec("PRAGMA synchronous = NORMAL;");
$sqlite->exec("PRAGMA foreign_keys = OFF;");

$content = file_get_contents($dumpFile);

// Remove MySQL conditional comments /*!... */
$content = preg_replace('/\/\*![\s\S]*?\*\/;/m', '', $content);
$content = preg_replace('/\/\*![\s\S]*?\*\//m', '', $content);

// Remove LOCK / UNLOCK TABLES
$content = preg_replace('/^LOCK TABLES .*;/m', '', $content);
$content = preg_replace('/^UNLOCK TABLES;/m', '', $content);

// Extract CREATE TABLE statements and INSERT statements
$lines = explode("\n", $content);
$buffer = '';
$inCreate = false;
$currentTable = '';
$tableColumns = [];
$insertStatements = [];
$createStatements = [];

foreach ($lines as $line) {
    $trimmed = trim($line);
    if (empty($trimmed) || str_starts_with($trimmed, '--')) {
        continue;
    }

    if (str_starts_with(strtoupper($trimmed), 'DROP TABLE IF EXISTS')) {
        // e.g. DROP TABLE IF EXISTS `accounts`;
        $createStatements[] = $trimmed;
        continue;
    }

    if (str_starts_with(strtoupper($trimmed), 'CREATE TABLE')) {
        $inCreate = true;
        $buffer = $line . "\n";
        if (preg_match('/CREATE TABLE `?([a-zA-Z0-9_]+)`?/i', $trimmed, $m)) {
            $currentTable = $m[1];
        }
        continue;
    }

    if ($inCreate) {
        $buffer .= $line . "\n";
        if (str_ends_with($trimmed, ';')) {
            $inCreate = false;
            $createStatements[] = convertCreateTableToSqlite($buffer, $currentTable);
            $buffer = '';
        }
        continue;
    }

    if (str_starts_with(strtoupper($trimmed), 'INSERT INTO')) {
        $insertStatements[] = $trimmed;
    }
}

function convertCreateTableToSqlite(string $sql, string $tableName): string {
    // Extract inner columns
    $firstParen = strpos($sql, '(');
    $lastParen = strrpos($sql, ')');
    if ($firstParen === false || $lastParen === false) return $sql;

    $inner = substr($sql, $firstParen + 1, $lastParen - $firstParen - 1);
    $lines = explode("\n", $inner);
    $newDefs = [];
    $hasAutoPk = false;

    foreach ($lines as $rawLine) {
        $l = trim($rawLine, " \t\r,");
        if (empty($l)) continue;

        // Skip MySQL KEY, CONSTRAINT, UNIQUE KEY (we will add unique separately)
        if (preg_match('/^(KEY|INDEX|CONSTRAINT)/i', $l)) {
            continue;
        }

        if (preg_match('/^PRIMARY KEY\s*\(([^)]+)\)/i', $l, $pkMatch)) {
            $pkCol = trim($pkMatch[1], " `");
            // If already handled in column definition, skip
            if ($hasAutoPk) {
                continue;
            }
            $newDefs[] = "PRIMARY KEY (`{$pkCol}`)";
            continue;
        }

        if (preg_match('/^UNIQUE KEY\s*`?[^`\s]+`?\s*\(([^)]+)\)/i', $l, $ukMatch)) {
            $ukCol = trim($ukMatch[1]);
            $newDefs[] = "UNIQUE ({$ukCol})";
            continue;
        }

        // Column definition
        if (preg_match('/^`([^`]+)`\s+([a-zA-Z0-9_]+(\([^)]+\))?)(.*)$/i', $l, $colMatch)) {
            $colName = $colMatch[1];
            $colType = strtolower($colMatch[2]);
            $rest = $colMatch[4];

            $isAuto = str_contains(strtolower($rest), 'auto_increment');
            if ($isAuto) {
                $hasAutoPk = true;
                $newDefs[] = "`{$colName}` INTEGER PRIMARY KEY AUTOINCREMENT";
                continue;
            }

            // Map data types
            if (str_contains($colType, 'int')) {
                $sqliteType = 'INTEGER';
            } elseif (str_contains($colType, 'decimal') || str_contains($colType, 'float') || str_contains($colType, 'double')) {
                $sqliteType = 'NUMERIC';
            } else {
                $sqliteType = 'TEXT';
            }

            // Clean up rest
            $rest = preg_replace('/DEFAULT\s+current_timestamp\(\)/i', 'DEFAULT CURRENT_TIMESTAMP', $rest);
            $rest = preg_replace('/ON UPDATE\s+[a-zA-Z0-9_()]+/i', '', $rest);
            $rest = preg_replace('/COMMENT\s+\'[^\']*\'/i', '', $rest);
            $rest = preg_replace('/COLLATE\s+[a-zA-Z0-9_]+/i', '', $rest);
            $rest = preg_replace('/CHARACTER SET\s+[a-zA-Z0-9_]+/i', '', $rest);
            $rest = preg_replace('/unsigned/i', '', $rest);
            $rest = trim($rest);

            $newDefs[] = "`{$colName}` {$sqliteType} {$rest}";
        } else {
            $newDefs[] = $l;
        }
    }

    return "CREATE TABLE IF NOT EXISTS `{$tableName}` (\n  " . implode(",\n  ", $newDefs) . "\n);";
}

echo "Executing " . count($createStatements) . " DDL statements...\n";
foreach ($createStatements as $stmt) {
    if (empty(trim($stmt))) continue;
    try {
        $sqlite->exec($stmt);
    } catch (Exception $e) {
        echo "FAILING DDL: \n" . $stmt . "\n";
        throw $e;
    }
}

echo "Executing " . count($insertStatements) . " Data Insert statements...\n";
$sqlite->beginTransaction();
$totalRows = 0;
foreach ($insertStatements as $stmt) {
    if (empty(trim($stmt))) continue;
    $sqlite->exec($stmt);
    $totalRows++;
}
$sqlite->commit();

$sqlite->exec("PRAGMA foreign_keys = ON;");

echo "===================================\n";
echo "SUCCESS: SQLite database generated!\n";
echo "File: {$sqliteFile}\n";
echo "Size: " . number_format(filesize($sqliteFile) / 1024, 2) . " KB\n";

// Verify tables
$tables = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
echo "Verified " . count($tables) . " tables in SQLite:\n";
foreach ($tables as $t) {
    $count = $sqlite->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
    printf(" - %-26s : %4d rows\n", $t, $count);
}
