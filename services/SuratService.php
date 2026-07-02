<?php

declare(strict_types=1);

namespace Service;

use Core\Database;
use Repository\{SuratRepository, PendudukRepository};

/**
 * SuratService — Logika bisnis pengajuan surat.
 */
final class SuratService
{
    private SuratRepository    $suratRepo;
    private PendudukRepository $pendudukRepo;

    public function __construct(private Database $db)
    {
        $this->suratRepo    = new SuratRepository($db);
        $this->pendudukRepo = new PendudukRepository($db);
    }

    // ── Warga: Ajukan Surat ───────────────────────────────────────────────

    public function ajukan(int $userId, array $data, array $files): array
    {
        // Cek penduduk
        $penduduk = $this->pendudukRepo->findByUserId($userId);
        if (!$penduduk) {
            return ['success' => false, 'message' => 'Data kependudukan Anda belum terdaftar. Hubungi Admin Desa.'];
        }

        // Cek jenis surat aktif
        $jenis = $this->db->fetchOne(
            'SELECT * FROM jenis_surat WHERE id = ? AND is_active = 1',
            [$data['jenis_surat_id']]
        );
        if (!$jenis) {
            return ['success' => false, 'message' => 'Jenis surat tidak valid.'];
        }

        return $this->db->transaction(function () use ($userId, $data, $files, $penduduk, $jenis) {
            $nomor = $this->suratRepo->generateNomor();

            $pengajuanId = $this->suratRepo->create([
                'nomor'          => $nomor,
                'user_id'        => $userId,
                'penduduk_id'    => $penduduk['id'],
                'jenis_surat_id' => $jenis['id'],
                'keperluan'      => $data['keperluan'],
                'catatan_pemohon'=> $data['catatan'] ?? '',
                'status'         => 'menunggu',
            ]);

            // Upload lampiran
            $this->uploadLampiran($pengajuanId, $files);

            // Notifikasi admin
            $this->notifikasiAdmin("Pengajuan surat baru: $nomor — {$jenis['nama']}", 'surat');

            // Log
            $this->log($userId, 'ajukan_surat', 'surat', "Mengajukan $nomor");

            return ['success' => true, 'message' => 'Pengajuan berhasil.', 'nomor' => $nomor, 'id' => $pengajuanId];
        });
    }

    private function uploadLampiran(int $pengajuanId, array $files): void
    {
        $dir = UPLOAD_PATH . '/surat/' . date('Y/m');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        foreach ($files as $key => $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) continue;
            if ($file['size'] > MAX_FILE_SIZE)      continue;
            if (!in_array($file['type'], ALLOWED_DOC, true)) continue;

            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = bin2hex(random_bytes(16)) . '.' . $ext;
            $dest     = $dir . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $this->suratRepo->addLampiran([
                    'pengajuan_id'  => $pengajuanId,
                    'nama_file'     => $filename,
                    'original_name' => $file['name'],
                    'mime_type'     => $file['type'],
                    'ukuran'        => $file['size'],
                    'path'          => str_replace(ROOT_PATH, '', $dest),
                    'jenis_lampiran'=> $key,
                ]);
            }
        }
    }

    // ── Admin: Verifikasi ─────────────────────────────────────────────────

    public function verifikasi(int $pengajuanId, int $adminId, string $catatan = ''): array
    {
        $pengajuan = $this->suratRepo->findById($pengajuanId);
        if (!$pengajuan) return ['success' => false, 'message' => 'Pengajuan tidak ditemukan.'];
        if ($pengajuan['status'] !== 'menunggu') return ['success' => false, 'message' => 'Status tidak sesuai.'];

        $this->suratRepo->updateStatus($pengajuanId, 'diverifikasi', [
            'admin_id'      => $adminId,
            'catatan_admin' => $catatan,
            'verified_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->notifikasiUser($pengajuan['user_id'], "Pengajuan {$pengajuan['nomor']} telah diverifikasi Admin.", 'surat');
        $this->log($adminId, 'verifikasi_surat', 'surat', "Verifikasi {$pengajuan['nomor']}");

        return ['success' => true, 'message' => 'Pengajuan berhasil diverifikasi.'];
    }

    public function kirimKeKades(int $pengajuanId, int $adminId): array
    {
        $pengajuan = $this->suratRepo->findById($pengajuanId);
        if (!$pengajuan) return ['success' => false, 'message' => 'Pengajuan tidak ditemukan.'];

        $this->suratRepo->updateStatus($pengajuanId, 'menunggu_persetujuan', ['admin_id' => $adminId]);
        $this->notifikasiKades("Ada surat menunggu persetujuan: {$pengajuan['nomor']}", 'surat');
        $this->log($adminId, 'kirim_kades', 'surat', "Kirim ke Kades {$pengajuan['nomor']}");

        return ['success' => true, 'message' => 'Pengajuan dikirim ke Kepala Desa.'];
    }

    public function tolakAdmin(int $pengajuanId, int $adminId, string $alasan): array
    {
        $pengajuan = $this->suratRepo->findById($pengajuanId);
        if (!$pengajuan) return ['success' => false, 'message' => 'Pengajuan tidak ditemukan.'];

        $this->suratRepo->updateStatus($pengajuanId, 'ditolak', [
            'admin_id'      => $adminId,
            'catatan_admin' => $alasan,
            'rejected_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->notifikasiUser($pengajuan['user_id'], "Maaf, pengajuan {$pengajuan['nomor']} ditolak. Alasan: $alasan", 'surat');
        $this->log($adminId, 'tolak_surat', 'surat', "Tolak {$pengajuan['nomor']}");

        return ['success' => true, 'message' => 'Pengajuan ditolak.'];
    }

    // ── Kepala Desa: Persetujuan ──────────────────────────────────────────

    public function setujui(int $pengajuanId, int $kadesId, string $catatan = ''): array
    {
        $pengajuan = $this->suratRepo->findById($pengajuanId);
        if (!$pengajuan) return ['success' => false, 'message' => 'Pengajuan tidak ditemukan.'];
        if ($pengajuan['status'] !== 'menunggu_persetujuan') return ['success' => false, 'message' => 'Status tidak sesuai.'];

        $qrCode = $this->generateQrCode($pengajuan['nomor']);

        $this->suratRepo->updateStatus($pengajuanId, 'selesai', [
            'kades_id'     => $kadesId,
            'catatan_kades'=> $catatan,
            'approved_at'  => date('Y-m-d H:i:s'),
            'selesai_at'   => date('Y-m-d H:i:s'),
            'qr_code'      => $qrCode,
        ]);

        $this->notifikasiUser($pengajuan['user_id'], "Surat {$pengajuan['nomor']} sudah dapat diunduh.", 'surat');
        $this->log($kadesId, 'setujui_surat', 'surat', "Setujui {$pengajuan['nomor']}");

        return ['success' => true, 'message' => 'Surat berhasil disetujui dan siap diunduh.'];
    }

    public function tolakKades(int $pengajuanId, int $kadesId, string $alasan): array
    {
        $pengajuan = $this->suratRepo->findById($pengajuanId);
        if (!$pengajuan) return ['success' => false, 'message' => 'Pengajuan tidak ditemukan.'];

        $this->suratRepo->updateStatus($pengajuanId, 'ditolak', [
            'kades_id'     => $kadesId,
            'catatan_kades'=> $alasan,
            'rejected_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->notifikasiUser($pengajuan['user_id'], "Surat {$pengajuan['nomor']} ditolak Kepala Desa. Alasan: $alasan", 'surat');

        return ['success' => true, 'message' => 'Pengajuan ditolak.'];
    }

    private function generateQrCode(string $nomor): string
    {
        return 'QR-' . strtoupper(bin2hex(random_bytes(8))) . '-' . $nomor;
    }

    // ── Getters ───────────────────────────────────────────────────────────

    public function getJenisSurat(): array
    {
        return $this->db->fetchAll('SELECT * FROM jenis_surat WHERE is_active = 1 ORDER BY nama');
    }

    public function getByUser(int $userId, array $filters = [], int $page = 1): array
    {
        return $this->suratRepo->findByUserId($userId, $filters, $page);
    }

    public function getAll(array $filters = [], int $page = 1): array
    {
        return $this->suratRepo->findAll($filters, $page);
    }

    public function getById(int $id): array|false
    {
        return $this->suratRepo->findById($id);
    }

    public function getDashboardStats(): array
    {
        $byStatus = $this->suratRepo->countByStatus();
        $stats    = ['total' => 0, 'menunggu' => 0, 'selesai' => 0, 'ditolak' => 0];

        foreach ($byStatus as $row) {
            $stats['total'] += $row['total'];
            if (isset($stats[$row['status']])) {
                $stats[$row['status']] = $row['total'];
            }
        }
        return $stats;
    }

    // ── Notifikasi internal ───────────────────────────────────────────────

    private function notifikasiUser(int $userId, string $pesan, string $tipe): void
    {
        $this->db->insert('notifikasi', [
            'user_id' => $userId,
            'judul'   => 'Update Pengajuan Surat',
            'pesan'   => $pesan,
            'tipe'    => $tipe,
        ]);
    }

    private function notifikasiAdmin(string $pesan, string $tipe): void
    {
        $admins = $this->db->fetchAll(
            'SELECT u.id FROM users u JOIN roles r ON r.id = u.role_id WHERE r.name = ? AND u.is_active = 1',
            ['admin']
        );
        foreach ($admins as $admin) {
            $this->notifikasiUser($admin['id'], $pesan, $tipe);
        }
    }

    private function notifikasiKades(string $pesan, string $tipe): void
    {
        $kades = $this->db->fetchAll(
            'SELECT u.id FROM users u JOIN roles r ON r.id = u.role_id WHERE r.name = ? AND u.is_active = 1',
            ['kepala_desa']
        );
        foreach ($kades as $k) {
            $this->notifikasiUser($k['id'], $pesan, $tipe);
        }
    }

    private function log(int $userId, string $action, string $module, string $desc): void
    {
        $this->db->insert('log_aktivitas', [
            'user_id'     => $userId,
            'action'      => $action,
            'module'      => $module,
            'description' => $desc,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
