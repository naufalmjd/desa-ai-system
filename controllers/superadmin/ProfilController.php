<?php

declare(strict_types=1);

namespace Controller\Superadmin;

use Core\Controller;
use Service\AuthService;
use Repository\UserRepository;

/**
 * ProfilController — Kelola Akun Super Admin
 */
final class ProfilController extends Controller
{
    private AuthService $authSvc;
    private UserRepository $userRepo;

    public function __construct()
    {
        parent::__construct();
        $this->authSvc = new AuthService($this->db, $this->session);
        $this->userRepo = new UserRepository($this->db);
    }

    // GET /superadmin/profil
    public function index(): void
    {
        $user = $this->db->fetchOne('SELECT * FROM users WHERE id = ?', [$this->authId()]);
        $flash = $this->getFlash();
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('superadmin/profil/index', compact('user', 'flash', 'csrfToken'), 'superadmin');
    }

    // POST /superadmin/profil/update
    public function update(): void
    {
        $userId = $this->authId();
        $username = trim($this->clean($this->input('username', '')));
        $email = trim($this->clean($this->input('email', '')));

        if ($username === '' || $email === '') {
            $this->flash('danger', 'Username dan Email tidak boleh kosong.');
            $this->redirect('superadmin/profil');
        }

        // Cek duplikat username
        $existingUser = $this->db->fetchOne('SELECT * FROM users WHERE username = ? AND id != ? AND deleted_at IS NULL', [$username, $userId]);
        if ($existingUser) {
            $this->flash('danger', 'Username sudah digunakan.');
            $this->redirect('superadmin/profil');
        }

        // Cek duplikat email
        $existingEmail = $this->db->fetchOne('SELECT * FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL', [$email, $userId]);
        if ($existingEmail) {
            $this->flash('danger', 'Email sudah digunakan.');
            $this->redirect('superadmin/profil');
        }

        $this->db->update('users', [
            'username' => $username,
            'email' => $email,
        ], 'id = ?', [$userId]);

        // Update session
        $userSession = $this->session->get('user');
        $userSession['username'] = $username;
        $userSession['email'] = $email;
        $userSession['nama'] = $username;
        $this->session->set('user', $userSession);

        $this->logActivity('Memperbarui profil Super Admin');
        $this->flash('success', 'Profil berhasil diperbarui.');
        $this->redirect('superadmin/profil');
    }

    // POST /superadmin/profil/changePassword
    public function changePassword(): void
    {
        $userId = $this->authId();
        $oldPass = $this->input('old_password', '');
        $newPass = $this->input('new_password', '');
        $confirmPass = $this->input('confirm_password', '');

        if ($oldPass === '' || $newPass === '' || $confirmPass === '') {
            $this->flash('danger', 'Semua field password wajib diisi.');
            $this->redirect('superadmin/profil');
        }

        if ($newPass !== $confirmPass) {
            $this->flash('danger', 'Password baru dan konfirmasi password tidak cocok.');
            $this->redirect('superadmin/profil');
        }

        if (strlen($newPass) < 6) {
            $this->flash('danger', 'Password baru minimal 6 karakter.');
            $this->redirect('superadmin/profil');
        }

        $result = $this->authSvc->changePassword($userId, $oldPass, $newPass);

        if ($result['success']) {
            $this->logActivity('Mengubah password Super Admin');
            $this->flash('success', $result['message']);
        } else {
            $this->flash('danger', $result['message']);
        }

        $this->redirect('superadmin/profil');
    }

    private function logActivity(string $desc): void
    {
        $this->db->insert('log_aktivitas', [
            'user_id'     => $this->authId(),
            'action'      => 'ubah_profil',
            'module'      => 'superadmin',
            'description' => $desc,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
