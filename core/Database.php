<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * Database — Singleton PDO wrapper dengan query builder sederhana.
 */
final class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $cfg = require CONFIG_PATH . '/database.php';

        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['driver'],
            $cfg['host'],
            $cfg['port'],
            $cfg['dbname'],
            $cfg['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options']);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    // ── Query helpers ──────────────────────────────────────────────────────

    /** Jalankan query dan kembalikan PDOStatement */
    public function query(string $sql, array $bindings = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    /** Ambil semua baris */
    public function fetchAll(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetchAll();
    }

    /** Ambil satu baris */
    public function fetchOne(string $sql, array $bindings = []): array|false
    {
        return $this->query($sql, $bindings)->fetch();
    }

    /** Ambil satu kolom dari satu baris */
    public function fetchColumn(string $sql, array $bindings = []): mixed
    {
        return $this->query($sql, $bindings)->fetchColumn();
    }

    /** INSERT — kembalikan last insert id */
    public function insert(string $table, array $data): string
    {
        $cols  = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $marks = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO `$table` ($cols) VALUES ($marks)", array_values($data));
        return $this->pdo->lastInsertId();
    }

    /** UPDATE — kembalikan jumlah baris terpengaruh */
    public function update(string $table, array $data, string $where, array $bindings = []): int
    {
        $set  = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($data)));
        $stmt = $this->query(
            "UPDATE `$table` SET $set WHERE $where",
            [...array_values($data), ...$bindings]
        );
        return $stmt->rowCount();
    }

    /** DELETE lunak (soft delete) */
    public function softDelete(string $table, string $where, array $bindings = []): int
    {
        return $this->update($table, ['deleted_at' => date('Y-m-d H:i:s')], $where, $bindings);
    }

    /** Hard DELETE */
    public function delete(string $table, string $where, array $bindings = []): int
    {
        $stmt = $this->query("DELETE FROM `$table` WHERE $where", $bindings);
        return $stmt->rowCount();
    }

    // ── Transaction ───────────────────────────────────────────────────────

    public function beginTransaction(): void  { $this->pdo->beginTransaction(); }
    public function commit(): void            { $this->pdo->commit(); }
    public function rollback(): void          { $this->pdo->rollBack(); }

    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    // ── Pagination ────────────────────────────────────────────────────────

    public function paginate(string $sql, array $bindings, int $page, int $perPage = PER_PAGE): array
    {
        $total  = (int)$this->fetchColumn("SELECT COUNT(*) FROM ($sql) AS t", $bindings);
        $offset = ($page - 1) * $perPage;
        $items  = $this->fetchAll("$sql LIMIT $perPage OFFSET $offset", $bindings);

        return [
            'data'         => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int)ceil($total / $perPage),
        ];
    }

    // Cegah clone & unserialize
    private function __clone() {}
    public function __wakeup(): void { throw new RuntimeException('Cannot unserialize singleton.'); }
}
