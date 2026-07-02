<?php

declare(strict_types=1);

namespace Controller\Warga;

use Core\Controller;
use Repository\PendudukRepository;

/**
 * ProfilController — Mengelola data profil warga.
 */
final class ProfilController extends Controller
{
    public function index(): void
    {
        $userId = $this->authId();
        $pendudukRepo = new PendudukRepository($this->db);
        $penduduk = $pendudukRepo->findByUserId($userId);
        $user = $this->auth();
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('warga/profil/index', compact('penduduk', 'user', 'csrfToken'), 'warga');
    }

    // POST /warga/profil/update
    public function update(): void
    {
        $userId = $this->authId();
        $pendudukRepo = new PendudukRepository($this->db);
        $penduduk = $pendudukRepo->findByUserId($userId);

        // Data yang boleh diubah sendiri oleh warga (data pribadi ringan).
        // NIK, No. KK, Tempat/Tanggal Lahir, Alamat KTP sengaja TIDAK bisa
        // diubah sendiri — itu data resmi kependudukan yang hanya boleh
        // diubah oleh Admin Desa lewat menu Kelola Penduduk.
        $nama      = trim($this->clean($this->input('nama', '')));
        $noHp      = trim($this->clean($this->input('no_hp', '')));
        $pekerjaan = trim($this->clean($this->input('pekerjaan', '')));
        $email     = trim($this->clean($this->input('email', '')));

        if (!$nama) {
            $this->flash('danger', 'Nama tidak boleh kosong.');
            $this->redirect('warga/profil');
        }

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('danger', 'Format email tidak valid.');
            $this->redirect('warga/profil');
        }

        // Update data penduduk (kalau sudah terhubung)
        if ($penduduk) {
            $pendudukRepo->update((int)$penduduk['id'], [
                'nama'      => $nama,
                'no_hp'     => $noHp,
                'pekerjaan' => $pekerjaan ?: '-',
            ]);
        }

        // Update email di akun login (kalau diisi & beda)
        if ($email) {
            $this->db->update('users', ['email' => $email], 'id = ?', [$userId]);
        }

        // Refresh data di session biar nama baru langsung tampil di sidebar
        // tanpa perlu logout/login ulang
        $sessionUser           = $this->session->get('user');
        $sessionUser['nama']   = $nama;
        if ($email) {
            $sessionUser['email'] = $email;
        }
        $this->session->set('user', $sessionUser);

        $this->log($userId, 'update_profil', 'profil', 'Memperbarui data profil sendiri');

        $this->flash('success', 'Profil berhasil diperbarui.');
        $this->redirect('warga/profil');
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
