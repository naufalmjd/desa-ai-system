<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;

/**
 * LaporanController — Laporan statistik surat & pengaduan.
 */
final class LaporanController extends Controller
{
    public function index(): void
    {
        $year = (int)date('Y');
        
        // Count stats
        $stats = [
            'total_surat' => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengajuan_surat WHERE deleted_at IS NULL'),
            'total_pengaduan' => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengaduan WHERE deleted_at IS NULL'),
            'surat_selesai' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM pengajuan_surat WHERE status='selesai' AND deleted_at IS NULL"),
            'pengaduan_selesai' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM pengaduan WHERE status='selesai' AND deleted_at IS NULL")
        ];

        $user = $this->auth();
        $this->render('admin/laporan/index', compact('stats', 'user'), 'admin');
    }
}
