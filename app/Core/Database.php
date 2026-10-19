<?php
namespace App\Core;

use mysqli;
use PDO;
use Exception;

class Database {
    private static ?Database $instance = null;
    private string $driver = 'mysql';
    private ?mysqli $connection = null;
    private ?PDO $pdo = null;
    private bool $inTransaction = false;
    private int $lastAffectedRows = 0;
    private array $config = [];

    private function __construct() {
        $this->config = require dirname(__DIR__, 2) . '/config/database.php';
        $this->driver = strtolower($this->config['connection'] ?? 'mysql');

        if ($this->driver === 'sqlite') {
            $this->connectSqlite();
        } else {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->connectMysql();
        }
    }

    private function connectMysql(): void {
        try {
            $this->connection = new mysqli(
                $this->config['host'],
                $this->config['username'],
                $this->config['password'],
                $this->config['database'],
                $this->config['port']
            );
            $this->connection->set_charset($this->config['charset'] ?? 'utf8mb4');
        } catch (Exception $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please ensure MySQL is running or configure SQLite.");
        }
    }

    private function connectSqlite(): void {
        try {
            $path = $this->config['sqlite_path'] ?? (dirname(__DIR__, 2) . '/desktop/database/billing.sqlite');
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $this->pdo = new PDO(
                "sqlite:{$path}",
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            // SQLite optimizations and settings
            $this->pdo->exec("PRAGMA journal_mode = WAL;");
            $this->pdo->exec("PRAGMA synchronous = NORMAL;");
            $this->pdo->exec("PRAGMA foreign_keys = ON;");

            // Register MySQL-compatible SQL functions in SQLite
            $this->pdo->sqliteCreateFunction('DATE_FORMAT', function($date, $format) {
                if (!$date) return null;
                $time = strtotime((string)$date);
                if ($time === false) return null;
                $phpFormat = str_replace(
                    ['%Y', '%y', '%m', '%c', '%d', '%e', '%H', '%h', '%i', '%s', '%M', '%b'],
                    ['Y',  'y',  'm',  'n',  'd',  'j',  'H',  'h',  'i',  's',  'F',  'M'],
                    $format
                );
                return date($phpFormat, $time);
            });

            $this->pdo->sqliteCreateFunction('NOW', function() {
                return date('Y-m-d H:i:s');
            });

            $this->pdo->sqliteCreateFunction('CURDATE', function() {
                return date('Y-m-d');
            });

        } catch (Exception $e) {
            error_log("SQLite Connection Error: " . $e->getMessage());
            die("SQLite database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli|PDO {
        return $this->driver === 'sqlite' ? $this->pdo : $this->connection;
    }

    public function beginTransaction(): bool {
        $this->inTransaction = true;
        if ($this->driver === 'sqlite') {
            return $this->pdo->beginTransaction();
        }
        return $this->connection->begin_transaction();
    }

    public function commit(): bool {
        $this->inTransaction = false;
        if ($this->driver === 'sqlite') {
            return $this->pdo->commit();
        }
        return $this->connection->commit();
    }

    public function rollback(): bool {
        $this->inTransaction = false;
        if ($this->driver === 'sqlite') {
            return $this->pdo->rollBack();
        }
        return $this->connection->rollback();
    }

    public function query(string $sql, array $params = [], bool $retry = true): array|int|bool {
        if ($this->driver === 'sqlite') {
            return $this->querySqlite($sql, $params);
        }
        return $this->queryMysql($sql, $params, $retry);
    }

    private function querySqlite(string $sql, array $params): array|int|bool {
        try {
            // Translate MySQL-specific syntax to SQLite syntax
            if (stripos($sql, 'ON DUPLICATE KEY UPDATE') !== false) {
                // SQLite 3.24+ UPSERT syntax
                $sql = preg_replace(
                    '/ON DUPLICATE KEY UPDATE\s+(`?[a-zA-Z0-9_]+`?)\s*=\s*VALUES\((`?[a-zA-Z0-9_]+`?)\)/i',
                    'ON CONFLICT(`supplier_id`) DO UPDATE SET $1 = excluded.$2',
                    $sql
                );
                $sql = preg_replace('/VALUES\((`?[a-zA-Z0-9_]+`?)\)/i', 'excluded.$1', $sql);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $trimmedSql = strtoupper(trim($sql));
            if (str_starts_with($trimmedSql, 'SELECT') || str_starts_with($trimmedSql, 'PRAGMA') || str_starts_with($trimmedSql, 'EXPLAIN')) {
                return $stmt->fetchAll();
            }

            if (str_starts_with($trimmedSql, 'INSERT')) {
                $lastId = (int)$this->pdo->lastInsertId();
                $this->lastAffectedRows = $stmt->rowCount();
                return $lastId > 0 ? $lastId : $this->lastAffectedRows;
            }

            $this->lastAffectedRows = $stmt->rowCount();
            return $this->lastAffectedRows;
        } catch (Exception $e) {
            error_log("SQLite Query Error [{$sql}]: " . $e->getMessage());
            if ($this->inTransaction) {
                $this->rollback();
            }
            throw $e;
        }
    }

    private function queryMysql(string $sql, array $params, bool $retry): array|int|bool {
        try {
            if (empty($params)) {
                $result = $this->connection->query($sql);
                if ($result === true) {
                    return $this->connection->insert_id ?: $this->connection->affected_rows;
                }
                if ($result === false) {
                    return false;
                }
                $rows = [];
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                $result->free();
                return $rows;
            }

            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new Exception("MySQL Prepare failed: " . $this->connection->error);
            }

            $types = '';
            $bindParams = [];
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param) || is_double($param)) {
                    $types .= 'd';
                } elseif (is_null($param)) {
                    $types .= 's';
                } else {
                    $types .= 's';
                }
                $bindParams[] = $param;
            }

            $stmt->bind_param($types, ...$bindParams);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result === false) {
                $insertId = $stmt->insert_id;
                $affected = $stmt->affected_rows;
                $stmt->close();
                return $insertId > 0 ? $insertId : $affected;
            }

            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $stmt->close();
            return $rows;
        } catch (\mysqli_sql_exception $e) {
            if ($retry && !$this->inTransaction && ($e->getCode() === 2006 || $e->getCode() === 2013 || str_contains($e->getMessage(), 'gone away'))) {
                error_log("Database: MySQL server connection lost ({$e->getMessage()}), attempting reconnection...");
                $this->connectMysql();
                return $this->queryMysql($sql, $params, false);
            }
            error_log("Database Query Error [{$sql}]: " . $e->getMessage());
            if ($this->inTransaction) {
                $this->rollback();
            }
            throw $e;
        } catch (Exception $e) {
            error_log("Database Query Error [{$sql}]: " . $e->getMessage());
            if ($this->inTransaction) {
                $this->rollback();
            }
            throw $e;
        }
    }

    public function escape(string $value): string {
        if ($this->driver === 'sqlite') {
            return trim($this->pdo->quote($value), "'");
        }
        return $this->connection->real_escape_string($value);
    }

    public function getLastInsertId(): int|string {
        if ($this->driver === 'sqlite') {
            return (int)$this->pdo->lastInsertId();
        }
        return $this->connection->insert_id;
    }

    public function getAffectedRows(): int {
        if ($this->driver === 'sqlite') {
            return $this->lastAffectedRows;
        }
        return $this->connection->affected_rows;
    }
}
