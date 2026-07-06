<?php

declare(strict_types=1);

namespace Controller\Superadmin;

use Core\Controller;
use Repository\UserRepository;

/**
 * UserController — Kelola User Akun (Super Admin)
 */
final class UserController extends Controller
{
    private UserRepository $userRepo;

    public function __construct()
    {
        parent::__construct();
        $this->userRepo = new UserRepository($this->db);
    }

    // GET /superadmin/user
    public function index(): void
    {
        $filters = [
            'role_id' => $this->input('role_id') ? (int)$this->input('role_id') : null,
            'search'  => $this->clean($this->input('q', '')),
        ];

        $page   = max(1, (int)$this->input('page', 1));
        $result = $this->userRepo->findAll($filters, $page);
        $roles  = $this->db->fetchAll('SELECT * FROM roles');
        $flash  = $this->getFlash();

        $this->render('superadmin/user/index', compact('result', 'filters', 'roles', 'flash'), 'superadmin');
    }

    // GET /superadmin/user/create
    public function create(): void
    {
        $roles     = $this->db->fetchAll('SELECT * FROM roles');
        $csrfToken = $this->session->generateCsrfToken();
        $this->render('superadmin/user/create', compact('roles', 'csrfToken'), 'superadmin');
    }

    // POST /superadmin/user/store
    public function store(): void
    {
        $username = trim($this->clean($this->input('username', '')));
        $email    = trim($this->clean($this->input('email', '')));
        $password = $this->input('password', '');
        $roleId   = (int)$this->input('role_id');
        $isActive = (int)$this->input('is_active', 1);

        if (!$username || !$email || !$password || !$roleId) {
            $this->flash('danger', 'Semua field wajib diisi.');
            $this->redirect('superadmin/user/create');
        }

        // Cek duplikat
        if ($this->userRepo->findByUsername($username)) {
            $this->flash('danger', 'Username sudah digunakan.');
            $this->redirect('superadmin/user/create');
        }

        if ($this->userRepo->findByEmail($email)) {
            $this->flash('danger', 'Email sudah digunakan.');
            $this->redirect('superadmin/user/create');
        }

        $userId = $this->userRepo->create([
            'username'   => $username,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'role_id'    => $roleId,
            'is_active'  => $isActive,
            'email_verified' => 1,
        ]);

        $this->logActivity('Menambah user baru: ' . $username . ' (ID: ' . $userId . ')');
        $this->flash('success', 'User berhasil ditambahkan.');
        $this->redirect('superadmin/user');
    }

    // GET /superadmin/user/edit/{id}
    public function edit(int $id): void
    {
        $user = $this->db->fetchOne(
            'SELECT u.*, r.name AS role_name, r.label AS role_label
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = ? AND u.deleted_at IS NULL',
            [$id]
        );

        if (!$user) {
            $this->abort(404, 'User tidak ditemukan.');
        }

        $roles     = $this->db->fetchAll('SELECT * FROM roles');
        $csrfToken = $this->session->generateCsrfToken();
        $this->render('superadmin/user/edit', compact('user', 'roles', 'csrfToken'), 'superadmin');
    }

    // POST /superadmin/user/update/{id}
    public function update(int $id): void
    {
        $user = $this->db->fetchOne('SELECT * FROM users WHERE id = ? AND deleted_at IS NULL', [$id]);
        if (!$user) {
            $this->abort(404, 'User tidak ditemukan.');
        }

        $username = trim($this->clean($this->input('username', '')));
        $email    = trim($this->clean($this->input('email', '')));
        $password = $this->input('password', '');
        $roleId   = (int)$this->input('role_id');
        $isActive = (int)$this->input('is_active', 1);

        if (!$username || !$email || !$roleId) {
            $this->flash('danger', 'Username, email, dan role wajib diisi.');
            $this->redirect('superadmin/user/edit/' . $id);
        }

        // Cek duplikat username (jika diubah)
        if ($username !== $user['username'] && $this->userRepo->findByUsername($username)) {
            $this->flash('danger', 'Username sudah digunakan.');
            $this->redirect('superadmin/user/edit/' . $id);
        }

        // Cek duplikat email (jika diubah)
        if ($email !== $user['email'] && $this->userRepo->findByEmail($email)) {
            $this->flash('danger', 'Email sudah digunakan.');
            $this->redirect('superadmin/user/edit/' . $id);
        }

        $data = [
            'username'  => $username,
            'email'     => $email,
            'role_id'   => $roleId,
            'is_active' => $isActive,
        ];

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $this->userRepo->update($id, $data);

        $this->logActivity('Mengubah data user: ' . $username . ' (ID: ' . $id . ')');
        $this->flash('success', 'User berhasil diperbarui.');
        $this->redirect('superadmin/user');
    }

    // POST /superadmin/user/delete/{id}
    public function delete(int $id): void
    {
        // Cegah menghapus diri sendiri
        if ($id === $this->authId()) {
            if ($this->isAjax()) {
                $this->jsonError('Anda tidak dapat menghapus akun Anda sendiri.');
            }
            $this->flash('danger', 'Anda tidak dapat menghapus akun Anda sendiri.');
            $this->redirect('superadmin/user');
        }

        $user = $this->db->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
        if (!$user) {
            $this->abort(404, 'User tidak ditemukan.');
        }

        $this->userRepo->softDelete($id);
        $this->logActivity('Menghapus user: ' . $user['username'] . ' (ID: ' . $id . ')');

        if ($this->isAjax()) {
            $this->jsonSuccess(null, 'User berhasil dihapus.');
        }
        $this->flash('success', 'User berhasil dihapus.');
        $this->redirect('superadmin/user');
    }

    // POST /superadmin/user/toggleStatus/{id}
    public function toggleStatus(int $id): void
    {
        // Cegah menonaktifkan diri sendiri
        if ($id === $this->authId()) {
            if ($this->isAjax()) {
                $this->jsonError('Anda tidak dapat menonaktifkan akun Anda sendiri.');
            }
            $this->flash('danger', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            $this->redirect('superadmin/user');
        }

        $user = $this->db->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
        if (!$user) {
            $this->abort(404, 'User tidak ditemukan.');
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $this->userRepo->update($id, ['is_active' => $newStatus]);

        $actionWord = $newStatus ? 'mengaktifkan' : 'menonaktifkan';
        $this->logActivity('Mengubah status user ' . $user['username'] . ' menjadi: ' . ($newStatus ? 'Aktif' : 'Nonaktif'));

        if ($this->isAjax()) {
            $this->jsonSuccess(['is_active' => $newStatus], 'Status user berhasil diperbarui.');
        }
        $this->flash('success', 'Status user berhasil diperbarui.');
        $this->redirect('superadmin/user');
    }

    private function logActivity(string $desc): void
    {
        $this->db->insert('log_aktivitas', [
            'user_id'     => $this->authId(),
            'action'      => 'kelola_users',
            'module'      => 'superadmin',
            'description' => $desc,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
