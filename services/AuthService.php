<?php

declare(strict_types=1);

namespace Service;

use Core\{Database, Session};
use Repository\UserRepository;

/**
 * AuthService — Logika bisnis autentikasi.
 */
final class AuthService
{
    private UserRepository $userRepo;

    public function __construct(private Database $db, private Session $session)
    {
        $this->userRepo = new UserRepository($db);
    }

    // ── Login ──────────────────────────────────────────────────────────────

    public function login(string $identifier, string $password, bool $remember = false): array
    {
        $user = $this->userRepo->findByUsernameOrEmail($identifier);

        if (!$user) {
            return ['success' => false, 'message' => 'Username/email atau password salah.'];
        }

        // Cek akun aktif
        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Akun Anda dinonaktifkan. Hubungi Admin.'];
        }

        // Cek akun terkunci
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
            return ['success' => false, 'message' => "Akun terkunci selama {$remaining} menit lagi karena terlalu banyak percobaan login gagal."];
        }

        // Verifikasi password dengan bcrypt
        if (!password_verify($password, $user['password'])) {
            $attempts = (int)$user['login_attempts'] + 1;
            $lock     = null;

            if ($attempts >= LOGIN_MAX_ATTEMPTS) {
                $lock = date('Y-m-d H:i:s', strtotime('+' . LOGIN_LOCK_MINUTES . ' minutes'));
            }

            $this->userRepo->updateLoginAttempts($user['id'], $attempts, $lock);

            $remaining = LOGIN_MAX_ATTEMPTS - $attempts;
            if ($remaining > 0) {
                return ['success' => false, 'message' => "Password salah. {$remaining} percobaan tersisa sebelum akun dikunci."];
            }
            return ['success' => false, 'message' => 'Akun dikunci selama ' . LOGIN_LOCK_MINUTES . ' menit.'];
        }

        // Login berhasil
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->userRepo->updateLastLogin($user['id'], $ip);
        $this->session->login($user);

        // Remember me
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->userRepo->updateRememberToken($user['id'], $token);
            setcookie('remember_token', $token, time() + REMEMBER_LIFETIME, '/', '', APP_ENV === 'production', true);
        }

        // Log aktivitas
        $this->logActivity($user['id'], 'login', 'auth', 'User login berhasil', $ip);

        return [
            'success'   => true,
            'message'   => 'Login berhasil.',
            'role'      => $user['role_name'],
            'redirect'  => $this->getRedirectByRole($user['role_name']),
        ];
    }

    // ── Logout ────────────────────────────────────────────────────────────

    public function logout(): void
    {
        $userId = $this->session->get('user')['id'] ?? null;

        if ($userId) {
            // Hapus remember token
            $this->userRepo->updateRememberToken($userId, '');
            $this->logActivity($userId, 'logout', 'auth', 'User logout');
        }

        // Hapus cookie
        setcookie('remember_token', '', time() - 3600, '/');
        $this->session->destroy();
    }

    // ── Auto-login via remember token ──────────────────────────────────────

    public function autoLoginFromCookie(): bool
    {
        $token = $_COOKIE['remember_token'] ?? '';
        if (!$token) return false;

        $user = $this->userRepo->findByRememberToken($token);
        if (!$user) return false;

        $this->session->login($user);
        $this->userRepo->updateLastLogin($user['id'], $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        return true;
    }

    // ── Register warga baru ───────────────────────────────────────────────

    public function register(array $data): array
    {
        // Cek duplikat
        if ($this->userRepo->findByUsername($data['username'])) {
            return ['success' => false, 'message' => 'Username sudah digunakan.'];
        }
        if ($this->userRepo->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email sudah terdaftar.'];
        }

        $userId = $this->userRepo->create([
            'role_id'  => 1, // warga
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]),
            'is_active' => 1,
        ]);

        return ['success' => true, 'message' => 'Registrasi berhasil. Silakan login.', 'user_id' => $userId];
    }

    // ── Ganti password ────────────────────────────────────────────────────

    public function changePassword(int $userId, string $oldPass, string $newPass): array
    {
        $user = $this->userRepo->findById($userId);
        if (!$user || !password_verify($oldPass, $user['password'])) {
            return ['success' => false, 'message' => 'Password lama tidak sesuai.'];
        }

        $this->userRepo->update($userId, [
            'password' => password_hash($newPass, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]),
        ]);

        return ['success' => true, 'message' => 'Password berhasil diubah.'];
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function getRedirectByRole(string $role): string
    {
        return match($role) {
            'warga'       => APP_URL . '/warga/dashboard',
            'admin'       => APP_URL . '/admin/dashboard',
            'kepala_desa' => APP_URL . '/kepaladesa/dashboard',
            default       => APP_URL . '/login',
        };
    }

    private function logActivity(int $userId, string $action, string $module, string $desc, string $ip = ''): void
    {
        $this->db->insert('log_aktivitas', [
            'user_id'     => $userId,
            'action'      => $action,
            'module'      => $module,
            'description' => $desc,
            'ip_address'  => $ip ?: ($_SERVER['REMOTE_ADDR'] ?? ''),
            'user_agent'  => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ]);
    }
}
