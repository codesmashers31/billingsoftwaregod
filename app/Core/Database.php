<?php
namespace App\Core;

use mysqli;
use Exception;

class Database {
    private static ?Database $instance = null;
    private ?mysqli $connection = null;
    private bool $inTransaction = false;

    private array $config = [];

    private function __construct() {
        $this->config = require dirname(__DIR__, 2) . '/config/database.php';
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->connect();
    }

    private function connect(): void {
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
            die("Database connection failed. Please ensure MySQL is running in XAMPP and configuration is valid.");
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        return $this->connection;
    }

    public function beginTransaction(): bool {
        $this->inTransaction = true;
        return $this->connection->begin_transaction();
    }

    public function commit(): bool {
        $this->inTransaction = false;
        return $this->connection->commit();
    }

    public function rollback(): bool {
        $this->inTransaction = false;
        return $this->connection->rollback();
    }

    public function query(string $sql, array $params = [], bool $retry = true): array|int|bool {
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

            // Determine types
            $types = '';
            $bindParams = [];
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param) || is_double($param)) {
                    $types .= 'd';
                } elseif (is_null($param)) {
                    $types .= 's'; // MySQL accepts string null
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
            // Error 2006: Server has gone away, 2013: Lost connection
            if ($retry && !$this->inTransaction && ($e->getCode() === 2006 || $e->getCode() === 2013 || str_contains($e->getMessage(), 'gone away'))) {
                error_log("Database: MySQL server connection lost ({$e->getMessage()}), attempting reconnection...");
                $this->connect();
                return $this->query($sql, $params, false);
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
        return $this->connection->real_escape_string($value);
    }

    public function getLastInsertId(): int|string {
        return $this->connection->insert_id;
    }

    public function getAffectedRows(): int {
        return $this->connection->affected_rows;
    }
}
