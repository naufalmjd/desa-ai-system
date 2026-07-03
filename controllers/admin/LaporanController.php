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

    public function print(): void
    {
        $stats = [
            'total_surat' => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengajuan_surat WHERE deleted_at IS NULL'),
            'total_pengaduan' => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengaduan WHERE deleted_at IS NULL'),
            'surat_selesai' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM pengajuan_surat WHERE status='selesai' AND deleted_at IS NULL"),
            'pengaduan_selesai' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM pengaduan WHERE status='selesai' AND deleted_at IS NULL")
        ];

        $suratList = $this->db->fetchAll(
            'SELECT p.*, pd.nama AS pemohon_nama, js.nama AS jenis_nama FROM pengajuan_surat p 
             JOIN penduduk pd ON pd.id = p.penduduk_id 
             JOIN jenis_surat js ON js.id = p.jenis_surat_id
             WHERE p.deleted_at IS NULL ORDER BY p.created_at DESC LIMIT 20'
        );

        $pengaduanList = $this->db->fetchAll(
            'SELECT p.*, pd.nama AS pelapor_nama FROM pengaduan p 
             JOIN users u ON u.id = p.user_id 
             JOIN penduduk pd ON pd.user_id = u.id
             WHERE p.deleted_at IS NULL ORDER BY p.created_at DESC LIMIT 20'
        );

        $user = $this->auth();
        $this->render('admin/laporan/print', compact('stats', 'suratList', 'pengaduanList', 'user'));
    }

    public function excel(): void
    {
        $this->auth();
        
        $stats = [
            'total_surat' => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengajuan_surat WHERE deleted_at IS NULL'),
            'total_pengaduan' => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM pengaduan WHERE deleted_at IS NULL'),
            'surat_selesai' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM pengajuan_surat WHERE status='selesai' AND deleted_at IS NULL"),
            'pengaduan_selesai' => (int)$this->db->fetchColumn("SELECT COUNT(*) FROM pengaduan WHERE status='selesai' AND deleted_at IS NULL")
        ];

        $suratList = $this->db->fetchAll(
            'SELECT p.*, pd.nama AS pemohon_nama, js.nama AS jenis_nama FROM pengajuan_surat p 
             JOIN penduduk pd ON pd.id = p.penduduk_id 
             JOIN jenis_surat js ON js.id = p.jenis_surat_id
             WHERE p.deleted_at IS NULL ORDER BY p.created_at DESC'
        );

        $pengaduanList = $this->db->fetchAll(
            'SELECT p.*, pd.nama AS pelapor_nama FROM pengaduan p 
             JOIN users u ON u.id = p.user_id 
             JOIN penduduk pd ON pd.user_id = u.id
             WHERE p.deleted_at IS NULL ORDER BY p.created_at DESC'
        );

        $filename = "Laporan_Pelayanan_Desa_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Add BOM to support UTF-8 in Excel
        echo "\xEF\xBB\xBF";
        echo "sep=,\r\n";

        $output = fopen('php://output', 'w');

        // Title
        fputcsv($output, ["LAPORAN VOLUME PELAYANAN DESA SUKAMAJU"]);
        fputcsv($output, ["Periode", date('F Y')]);
        fputcsv($output, ["Dibuat Pada", date('d/m/Y H:i:s')]);
        fputcsv($output, []);

        // Stats
        fputcsv($output, ["RINGKASAN VOLUMETRIK PELAYANAN"]);
        fputcsv($output, ["Kategori Layanan", "Total Masuk", "Total Selesai", "Tingkat Penyelesaian"]);
        
        $pctSurat = $stats['total_surat'] > 0 ? round(($stats['surat_selesai'] / $stats['total_surat']) * 100, 1) . "%" : "0%";
        $pctAduan = $stats['total_pengaduan'] > 0 ? round(($stats['pengaduan_selesai'] / $stats['total_pengaduan']) * 100, 1) . "%" : "0%";
        
        fputcsv($output, ["Pengajuan Surat", $stats['total_surat'], $stats['surat_selesai'], $pctSurat]);
        fputcsv($output, ["Aduan Warga", $stats['total_pengaduan'], $stats['pengaduan_selesai'], $pctAduan]);
        fputcsv($output, []);

        // Letters details
        fputcsv($output, ["RINCIAN DATA PENGAJUAN SURAT ADMINISTRASI"]);
        fputcsv($output, ["No", "Jenis Surat", "Nama Pemohon", "Tanggal Pengajuan", "Status"]);
        $no = 1;
        foreach ($suratList as $s) {
            fputcsv($output, [
                $no++,
                $s['jenis_nama'],
                $s['pemohon_nama'],
                date('d/m/Y H:i', strtotime($s['created_at'])),
                strtoupper($s['status'])
            ]);
        }
        fputcsv($output, []);

        // Complaints details
        fputcsv($output, ["RINCIAN DATA PENGADUAN WARGA"]);
        fputcsv($output, ["No", "Kategori", "Nama Pelapor", "Judul Laporan", "Status"]);
        $no = 1;
        foreach ($pengaduanList as $p) {
            fputcsv($output, [
                $no++,
                $p['kategori'],
                $p['pelapor_nama'],
                $p['judul'],
                strtoupper($p['status'])
            ]);
        }

        fclose($output);
        exit;
    }
}
