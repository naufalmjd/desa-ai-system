<?php

declare(strict_types=1);

namespace Controller\Warga;

use Core\Controller;
use Repository\{SuratRepository, PengaduanRepository, PendudukRepository};

/**
 * Dashboard Warga — Halaman utama warga.
 */
final class DashboardController extends Controller
{
    public function index(): void
    {
        $userId       = $this->authId();
        $suratRepo    = new SuratRepository($this->db);
        $pengaduanRepo= new PengaduanRepository($this->db);
        $pendudukRepo = new PendudukRepository($this->db);

        $penduduk     = $pendudukRepo->findByUserId($userId);

        // Statistik personal
        $suratList    = $suratRepo->findByUserId($userId, [], 1);
        $pengaduanList= $pengaduanRepo->findByUserId($userId, 1);

        $stats = [
            'total_surat'     => $suratList['total'],
            'surat_proses'    => $this->db->fetchColumn(
                'SELECT COUNT(*) FROM pengajuan_surat WHERE user_id=? AND status NOT IN (\'selesai\',\'ditolak\') AND deleted_at IS NULL',
                [$userId]
            ),
            'surat_selesai'   => $this->db->fetchColumn(
                'SELECT COUNT(*) FROM pengajuan_surat WHERE user_id=? AND status=\'selesai\' AND deleted_at IS NULL',
                [$userId]
            ),
            'pengaduan_aktif' => $this->db->fetchColumn(
                'SELECT COUNT(*) FROM pengaduan WHERE user_id=? AND status NOT IN (\'selesai\',\'ditutup\') AND deleted_at IS NULL',
                [$userId]
            ),
        ];

        // Notifikasi belum dibaca
        $notifCount = (int)$this->db->fetchColumn(
            'SELECT COUNT(*) FROM notifikasi WHERE user_id=? AND is_read=0',
            [$userId]
        );

        // Berita terbaru
        $beritaTerbaru = $this->db->fetchAll(
            'SELECT * FROM berita WHERE status=\'publish\' AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 4'
        );

        // Surat terbaru
        $suratTerbaru = $this->db->fetchAll(
            'SELECT ps.*, js.nama AS jenis_nama FROM pengajuan_surat ps
             JOIN jenis_surat js ON js.id = ps.jenis_surat_id
             WHERE ps.user_id=? AND ps.deleted_at IS NULL ORDER BY ps.created_at DESC LIMIT 5',
            [$userId]
        );

        $flash = $this->getFlash();

        $this->render('warga/dashboard/index', compact(
            'penduduk', 'stats', 'notifCount', 'beritaTerbaru', 'suratTerbaru', 'flash'
        ), 'warga');
    }
}
