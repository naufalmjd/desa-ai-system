<?php

declare(strict_types=1);

namespace Repository;

use Core\Database;

/**
 * PendudukRepository — Operasi CRUD penduduk dengan prepared statements.
 */
final class PendudukRepository
{
    public function __construct(private Database $db) {}

    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM penduduk WHERE id = ? AND deleted_at IS NULL',
            [$id]
        );
    }

    public function findByNik(string $nik): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM penduduk WHERE nik = ? AND deleted_at IS NULL',
            [$nik]
        );
    }

    public function findByUserId(int $userId): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM penduduk WHERE user_id = ? AND deleted_at IS NULL',
            [$userId]
        );
    }

    public function findAll(array $filters = [], int $page = 1): array
    {
        $where    = ['deleted_at IS NULL'];
        $bindings = [];

        if (!empty($filters['search'])) {
            $s          = '%' . $filters['search'] . '%';
            $where[]    = '(nik LIKE ? OR nama LIKE ? OR no_kk LIKE ?)';
            $bindings   = array_merge($bindings, [$s, $s, $s]);
        }
        if (!empty($filters['jk'])) {
            $where[]    = 'jenis_kelamin = ?';
            $bindings[] = $filters['jk'];
        }
        if (!empty($filters['status'])) {
            $where[]    = 'status_penduduk = ?';
            $bindings[] = $filters['status'];
        }
        if (!empty($filters['dusun'])) {
            $where[]    = 'dusun = ?';
            $bindings[] = $filters['dusun'];
        }

        $sql = 'SELECT * FROM penduduk WHERE ' . implode(' AND ', $where) . ' ORDER BY nama ASC';
        return $this->db->paginate($sql, $bindings, $page);
    }

    public function create(array $data): int
    {
        return (int)$this->db->insert('penduduk', $data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->update('penduduk', $data, 'id = ?', [$id]) > 0;
    }

    public function softDelete(int $id): bool
    {
        return $this->db->softDelete('penduduk', 'id = ?', [$id]) > 0;
    }

    public function countByStatus(): array
    {
        return $this->db->fetchAll(
            'SELECT status_penduduk, COUNT(*) AS total
             FROM penduduk WHERE deleted_at IS NULL
             GROUP BY status_penduduk'
        );
    }

    public function countByAgama(): array
    {
        return $this->db->fetchAll(
            'SELECT agama, COUNT(*) AS total
             FROM penduduk WHERE deleted_at IS NULL
             GROUP BY agama ORDER BY total DESC'
        );
    }

    public function countTotal(): int
    {
        return (int)$this->db->fetchColumn(
            'SELECT COUNT(*) FROM penduduk WHERE deleted_at IS NULL AND status_penduduk IN (\'Tetap\',\'Sementara\')'
        );
    }
}
