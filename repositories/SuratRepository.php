<?php

declare(strict_types=1);

namespace Repository;

use Core\Database;

/**
 * SuratRepository — Operasi data pengajuan surat.
 */
final class SuratRepository
{
    public function __construct(private Database $db) {}

    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT ps.*, js.nama AS jenis_nama, js.kode, js.persyaratan, js.estimasi_hari,
                    p.nama AS pemohon_nama, p.nik, p.alamat, p.no_hp,
                    u.email AS pemohon_email,
                    ua.username AS admin_username,
                    uk.username AS kades_username
             FROM pengajuan_surat ps
             JOIN jenis_surat js  ON js.id  = ps.jenis_surat_id
             JOIN penduduk p      ON p.id   = ps.penduduk_id
             JOIN users u         ON u.id   = ps.user_id
             LEFT JOIN users ua   ON ua.id  = ps.admin_id
             LEFT JOIN users uk   ON uk.id  = ps.kades_id
             WHERE ps.id = ? AND ps.deleted_at IS NULL',
            [$id]
        );
    }

    public function findByNomor(string $nomor): array|false
    {
        return $this->db->fetchOne(
            'SELECT ps.*, js.nama AS jenis_nama, p.nama AS pemohon_nama
             FROM pengajuan_surat ps
             JOIN jenis_surat js ON js.id = ps.jenis_surat_id
             JOIN penduduk p     ON p.id  = ps.penduduk_id
             WHERE ps.nomor = ? AND ps.deleted_at IS NULL',
            [$nomor]
        );
    }

    public function findByUserId(int $userId, array $filters = [], int $page = 1): array
    {
        $where    = ['ps.user_id = ?', 'ps.deleted_at IS NULL'];
        $bindings = [$userId];

        if (!empty($filters['status'])) {
            $where[]    = 'ps.status = ?';
            $bindings[] = $filters['status'];
        }

        $sql = 'SELECT ps.*, js.nama AS jenis_nama
                FROM pengajuan_surat ps
                JOIN jenis_surat js ON js.id = ps.jenis_surat_id
                WHERE ' . implode(' AND ', $where) . ' ORDER BY ps.created_at DESC';

        return $this->db->paginate($sql, $bindings, $page);
    }

    public function findAll(array $filters = [], int $page = 1): array
    {
        $where    = ['ps.deleted_at IS NULL'];
        $bindings = [];

        if (!empty($filters['status'])) {
            $where[]    = 'ps.status = ?';
            $bindings[] = $filters['status'];
        }
        if (!empty($filters['jenis_id'])) {
            $where[]    = 'ps.jenis_surat_id = ?';
            $bindings[] = $filters['jenis_id'];
        }
        if (!empty($filters['search'])) {
            $s          = '%' . $filters['search'] . '%';
            $where[]    = '(ps.nomor LIKE ? OR p.nama LIKE ? OR p.nik LIKE ?)';
            $bindings   = array_merge($bindings, [$s, $s, $s]);
        }
        if (!empty($filters['tanggal_dari'])) {
            $where[]    = 'DATE(ps.created_at) >= ?';
            $bindings[] = $filters['tanggal_dari'];
        }
        if (!empty($filters['tanggal_sampai'])) {
            $where[]    = 'DATE(ps.created_at) <= ?';
            $bindings[] = $filters['tanggal_sampai'];
        }

        $sql = 'SELECT ps.*, js.nama AS jenis_nama, p.nama AS pemohon_nama, p.nik
                FROM pengajuan_surat ps
                JOIN jenis_surat js ON js.id = ps.jenis_surat_id
                JOIN penduduk p     ON p.id  = ps.penduduk_id
                WHERE ' . implode(' AND ', $where) . ' ORDER BY ps.created_at DESC';

        return $this->db->paginate($sql, $bindings, $page);
    }

    public function create(array $data): int
    {
        return (int)$this->db->insert('pengajuan_surat', $data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->update('pengajuan_surat', $data, 'id = ?', [$id]) > 0;
    }

    public function updateStatus(int $id, string $status, array $extra = []): bool
    {
        return $this->update($id, array_merge(['status' => $status], $extra));
    }

    public function addLampiran(array $data): int
    {
        return (int)$this->db->insert('lampiran_surat', $data);
    }

    public function getLampiranByPengajuanId(int $pengajuanId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM lampiran_surat WHERE pengajuan_id = ?',
            [$pengajuanId]
        );
    }

    public function generateNomor(): string
    {
        $year  = date('Y');
        $last  = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM pengajuan_surat WHERE YEAR(created_at) = ?",
            [$year]
        );
        return sprintf('SRT-%s-%06d', $year, (int)$last + 1);
    }

    public function countByStatus(): array
    {
        return $this->db->fetchAll(
            'SELECT status, COUNT(*) AS total FROM pengajuan_surat
             WHERE deleted_at IS NULL GROUP BY status'
        );
    }

    public function monthlyStats(int $year): array
    {
        return $this->db->fetchAll(
            'SELECT MONTH(created_at) AS bulan, COUNT(*) AS total,
                    SUM(status = \'selesai\') AS selesai
             FROM pengajuan_surat
             WHERE YEAR(created_at) = ? AND deleted_at IS NULL
             GROUP BY MONTH(created_at)',
            [$year]
        );
    }
}
