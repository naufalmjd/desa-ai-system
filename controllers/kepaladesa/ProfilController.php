<?php

declare(strict_types=1);

namespace Controller\Kepaladesa;

use Core\Controller;
use Repository\PendudukRepository;

/**
 * ProfilController — Profil Kepala Desa.
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

        $this->render('kepaladesa/profil/index', compact('penduduk', 'user', 'csrfToken'), 'kepaladesa');
    }

    // POST /kepaladesa/profil/update
    public function update(): void
    {
        $userId = $this->authId();
        $pendudukRepo = new PendudukRepository($this->db);
        $penduduk = $pendudukRepo->findByUserId($userId);

        $nama  = trim($this->clean($this->input('nama', '')));
        $noHp  = trim($this->clean($this->input('no_hp', '')));
        $email = trim($this->clean($this->input('email', '')));

        if (!$nama) {
            $this->flash('danger', 'Nama tidak boleh kosong.');
            $this->redirect('kepaladesa/profil');
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('danger', 'Format email tidak valid.');
            $this->redirect('kepaladesa/profil');
        }

        if ($penduduk) {
            $pendudukRepo->update((int)$penduduk['id'], [
                'nama'  => $nama,
                'no_hp' => $noHp,
            ]);
        }
        if ($email) {
            $this->db->update('users', ['email' => $email], 'id = ?', [$userId]);
        }

        $sessionUser         = $this->session->get('user');
        $sessionUser['nama'] = $nama;
        if ($email) $sessionUser['email'] = $email;
        $this->session->set('user', $sessionUser);

        $this->db->insert('log_aktivitas', [
            'user_id'     => $userId,
            'action'      => 'update_profil',
            'module'      => 'profil',
            'description' => 'Memperbarui data profil sendiri',
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);

        $this->flash('success', 'Profil berhasil diperbarui.');
        $this->redirect('kepaladesa/profil');
    }
}
