<?php

declare(strict_types=1);

namespace Controller\Kepaladesa;

use Core\Controller;
use Repository\{SuratRepository, PengaduanRepository, PendudukRepository};

/**
 * Dashboard Eksekutif Kepala Desa.
 */
final class DashboardController extends Controller
{
    public function index(): void
    {
        $suratRepo     = new SuratRepository($this->db);
        $pengaduanRepo = new PengaduanRepository($this->db);
        $pendudukRepo  = new PendudukRepository($this->db);
        $year          = (int)date('Y');

        $stats = [
            'total_penduduk'      => $pendudukRepo->countTotal(),
            'surat_ttd'           => (int)$this->db->fetchColumn(
                "SELECT COUNT(*) FROM pengajuan_surat WHERE status='menunggu_persetujuan' AND deleted_at IS NULL"
            ),
            'pengaduan_kritis'    => (int)$this->db->fetchColumn(
                "SELECT COUNT(*) FROM pengaduan WHERE prioritas='kritis' AND status!='selesai' AND deleted_at IS NULL"
            ),
            'penyelesaian_aduan'  => $this->calcPenyelesaian(),
        ];

        // Chart data
        $aduanMonthly  = $pengaduanRepo->monthlyStats($year);
        $suratMonthly  = $suratRepo->monthlyStats($year);
        $aduanKategori = $pengaduanRepo->countByKategori();

        // Surat menunggu TTD
        $suratMenunggu = $this->db->fetchAll(
            "SELECT ps.*, js.nama AS jenis_nama, p.nama AS pemohon_nama, p.nik
             FROM pengajuan_surat ps
             JOIN jenis_surat js ON js.id = ps.jenis_surat_id
             JOIN penduduk p     ON p.id  = ps.penduduk_id
             WHERE ps.status='menunggu_persetujuan' AND ps.deleted_at IS NULL
             ORDER BY ps.created_at ASC LIMIT 10"
        );

        // Pengaduan prioritas tinggi
        $pengaduanKritis = $this->db->fetchAll(
            "SELECT pg.*, p.nama AS pelapor_nama, hai.confidence_score
             FROM pengaduan pg
             JOIN users u ON u.id = pg.user_id
             JOIN penduduk p ON p.user_id = u.id
             LEFT JOIN hasil_ai hai ON hai.pengaduan_id = pg.id
             WHERE pg.prioritas IN ('kritis','tinggi') AND pg.status != 'selesai' AND pg.deleted_at IS NULL
             ORDER BY FIELD(pg.prioritas,'kritis','tinggi') ASC
             LIMIT 10"
        );

        // KPI Radar data
        $kpiData = $this->getKpiData();
        $flash   = $this->getFlash();

        $this->render('kepaladesa/dashboard/index', compact(
            'stats', 'aduanMonthly', 'suratMonthly', 'aduanKategori',
            'suratMenunggu', 'pengaduanKritis', 'kpiData', 'flash'
        ), 'kepaladesa');
    }

    private function calcPenyelesaian(): float
    {
        $total   = (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengaduan WHERE deleted_at IS NULL');
        $selesai = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM pengaduan WHERE status='selesai' AND deleted_at IS NULL");
        return $total > 0 ? round($selesai / $total * 100, 1) : 0;
    }

    private function getKpiData(): array
    {
        return [
            ['subject' => 'Infrastruktur', 'value' => 78],
            ['subject' => 'Kebersihan',    'value' => 65],
            ['subject' => 'Keamanan',      'value' => 88],
            ['subject' => 'Kesehatan',     'value' => 72],
            ['subject' => 'Ekonomi',       'value' => 81],
            ['subject' => 'Pendidikan',    'value' => 91],
        ];
    }
}
