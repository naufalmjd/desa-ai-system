<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;
use Repository\{SuratRepository, PengaduanRepository, PendudukRepository};

/**
 * Dashboard Admin — Statistik & ringkasan data.
 */
final class DashboardController extends Controller
{
    public function index(): void
    {
        $suratRepo     = new SuratRepository($this->db);
        $pengaduanRepo = new PengaduanRepository($this->db);
        $pendudukRepo  = new PendudukRepository($this->db);

        $year = (int)date('Y');

        $stats = [
            'total_penduduk'   => $pendudukRepo->countTotal(),
            'surat_menunggu'   => (int)$this->db->fetchColumn(
                'SELECT COUNT(*) FROM pengajuan_surat WHERE status=\'menunggu\' AND deleted_at IS NULL'
            ),
            'pengaduan_aktif'  => (int)$this->db->fetchColumn(
                'SELECT COUNT(*) FROM pengaduan WHERE status IN (\'menunggu\',\'ditindaklanjuti\',\'diproses\') AND deleted_at IS NULL'
            ),
            'penyelesaian_pct' => $this->calcPenyelesaian(),
        ];

        $suratStats    = $suratRepo->countByStatus();
        $pengaduanStats= $pengaduanRepo->countByKategori();
        $suratMonthly  = $suratRepo->monthlyStats($year);
        $aduanMonthly  = $pengaduanRepo->monthlyStats($year);

        // Pengajuan terbaru butuh aksi
        $suratPending = $this->db->fetchAll(
            'SELECT ps.*, js.nama AS jenis_nama, p.nama AS pemohon_nama, p.nik
             FROM pengajuan_surat ps
             JOIN jenis_surat js ON js.id = ps.jenis_surat_id
             JOIN penduduk p     ON p.id  = ps.penduduk_id
             WHERE ps.status=\'menunggu\' AND ps.deleted_at IS NULL
             ORDER BY ps.created_at DESC LIMIT 5'
        );

        $flash = $this->getFlash();

        $this->render('admin/dashboard/index', compact(
            'stats', 'suratStats', 'pengaduanStats',
            'suratMonthly', 'aduanMonthly', 'suratPending', 'flash'
        ), 'admin');
    }

    private function calcPenyelesaian(): float
    {
        $total   = (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengaduan WHERE deleted_at IS NULL');
        $selesai = (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengaduan WHERE status=\'selesai\' AND deleted_at IS NULL');
        return $total > 0 ? round($selesai / $total * 100, 1) : 0;
    }
}
