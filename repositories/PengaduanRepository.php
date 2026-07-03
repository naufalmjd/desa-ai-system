<?php

declare(strict_types=1);

namespace Repository;

use Core\Database;

/**
 * PengaduanRepository — Operasi data pengaduan masyarakat.
 */
final class PengaduanRepository
{
    public function __construct(private Database $db) {}

    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT pg.*, p.nama AS pelapor_nama, p.nik AS pelapor_nik,
                    hai.kategori_deteksi, hai.confidence_score, hai.prioritas_ai,
                    ua.username AS admin_username
             FROM pengaduan pg
             JOIN users u          ON u.id  = pg.user_id
             JOIN penduduk p       ON p.user_id = u.id
             LEFT JOIN hasil_ai hai ON hai.pengaduan_id = pg.id
             LEFT JOIN users ua    ON ua.id = pg.admin_id
             WHERE pg.id = ? AND pg.deleted_at IS NULL',
            [$id]
        );
    }

    public function findByUserId(int $userId, int $page = 1): array
    {
        return $this->db->paginate(
            'SELECT pg.*, hai.confidence_score, hai.prioritas_ai, hai.kategori_deteksi
             FROM pengaduan pg
             LEFT JOIN hasil_ai hai ON hai.pengaduan_id = pg.id
             WHERE pg.user_id = ? AND pg.deleted_at IS NULL
             ORDER BY pg.created_at DESC',
            [$userId],
            $page
        );
    }

    public function findAll(array $filters = [], int $page = 1): array
    {
        $where    = ['pg.deleted_at IS NULL'];
        $bindings = [];

        foreach (['status', 'kategori', 'prioritas'] as $f) {
            if (!empty($filters[$f])) {
                $where[]    = "pg.$f = ?";
                $bindings[] = $filters[$f];
            }
        }
        if (!empty($filters['search'])) {
            $s          = '%' . $filters['search'] . '%';
            $where[]    = '(pg.nomor LIKE ? OR pg.judul LIKE ? OR pg.lokasi_alamat LIKE ?)';
            $bindings   = array_merge($bindings, [$s, $s, $s]);
        }

        $sql = 'SELECT pg.*, p.nama AS pelapor_nama,
                       hai.confidence_score, hai.prioritas_ai
                FROM pengaduan pg
                JOIN users u          ON u.id  = pg.user_id
                JOIN penduduk p       ON p.user_id = u.id
                LEFT JOIN hasil_ai hai ON hai.pengaduan_id = pg.id
                WHERE ' . implode(' AND ', $where) . ' ORDER BY
                FIELD(pg.prioritas,"kritis","tinggi","sedang","rendah"), pg.created_at DESC';

        return $this->db->paginate($sql, $bindings, $page);
    }

    public function create(array $data): int
    {
        return (int)$this->db->insert('pengaduan', $data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->update('pengaduan', $data, 'id = ?', [$id]) > 0;
    }

    public function addMedia(array $data): int
    {
        return (int)$this->db->insert('pengaduan_media', $data);
    }

    public function getMedia(int $pengaduanId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM pengaduan_media WHERE pengaduan_id = ?',
            [$pengaduanId]
        );
    }

    public function saveHasilAi(array $data): int
    {
        // Upsert berdasarkan pengaduan_id
        $existing = $this->db->fetchOne(
            'SELECT id FROM hasil_ai WHERE pengaduan_id = ?',
            [$data['pengaduan_id']]
        );

        if ($existing) {
            $this->db->update('hasil_ai', $data, 'pengaduan_id = ?', [$data['pengaduan_id']]);
            return $existing['id'];
        }
        return (int)$this->db->insert('hasil_ai', $data);
    }

    public function getHasilAi(int $pengaduanId): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM hasil_ai WHERE pengaduan_id = ?',
            [$pengaduanId]
        );
    }

    public function generateNomor(): string
    {
        $year = date('Y');
        $last = $this->db->fetchColumn(
            'SELECT COUNT(*) FROM pengaduan WHERE YEAR(created_at) = ?',
            [$year]
        );
        return sprintf('ADU-%s-%06d', $year, (int)$last + 1);
    }

    public function countByStatus(): array
    {
        return $this->db->fetchAll(
            'SELECT status, COUNT(*) AS total FROM pengaduan
             WHERE deleted_at IS NULL GROUP BY status'
        );
    }

    public function countByKategori(): array
    {
        return $this->db->fetchAll(
            'SELECT kategori, COUNT(*) AS total FROM pengaduan
             WHERE deleted_at IS NULL GROUP BY kategori ORDER BY total DESC'
        );
    }

    public function monthlyStats(int $year): array
    {
        return $this->db->fetchAll(
            'SELECT MONTH(created_at) AS bulan, COUNT(*) AS total,
                    SUM(status = \'selesai\') AS selesai
             FROM pengaduan WHERE YEAR(created_at) = ? AND deleted_at IS NULL
             GROUP BY MONTH(created_at)',
            [$year]
        );
    }

    public function getMapData(): array
    {
        return $this->db->fetchAll(
            'SELECT id, nomor, judul, kategori, prioritas, status,
                    latitude, longitude, lokasi_alamat
             FROM pengaduan
             WHERE deleted_at IS NULL AND latitude IS NOT NULL AND longitude IS NOT NULL'
        );
    }
}
