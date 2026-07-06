<?php

declare(strict_types=1);

namespace Controller\Auth;

use Core\{Controller, Database, Session};
use Service\AuthService;
use Repository\UserRepository;
use Repository\PendudukRepository;

/**
 * AuthController — Login, Logout, Register.
 */
final class AuthController extends Controller
{
    private AuthService $authService;
    private UserRepository $userRepo;
    private PendudukRepository $pendudukRepo;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService($this->db, $this->session);
        $this->userRepo = new UserRepository($this->db);
        $this->pendudukRepo = new PendudukRepository($this->db);
    }

    // GET /login
    public function login(): void
    {
        // Sudah login → redirect ke dashboard
        if ($this->session->isLoggedIn()) {
            $this->redirect($this->getDashboardUrl());
        }

        // Generate CSRF
        $csrfToken = $this->session->generateCsrfToken();
        $flash     = $this->getFlash();

        // Ambil daftar user untuk ditampilkan di halaman login
        $users = $this->userRepo->findAllWithRole();

        $this->render('auth/login', compact('csrfToken', 'flash', 'users'), '');
    }

    // POST /auth/loginPost
    public function loginPost(): void
    {
        $identifier = trim($this->clean($this->input('identifier', '')));
        $password   = $this->input('password', '');
        $remember   = (bool)$this->input('remember', false);

        if (!$identifier || !$password) {
            $this->flash('danger', 'Username/email dan password wajib diisi.');
            $this->redirect('login');
        }

        $result = $this->authService->login($identifier, $password, $remember);

        if ($result['success']) {
            $this->redirect($result['redirect']);
        }

        $this->flash('danger', $result['message']);
        $this->redirect('login');
    }

    // GET /auth/register-warga
    public function registerWarga(): void
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->isLoggedIn()) {
            $this->redirect($this->getDashboardUrl());
        }

        // Generate CSRF
        $csrfToken = $this->session->generateCsrfToken();
        $flash     = $this->getFlash();

        $this->render('auth/register-warga', compact('csrfToken', 'flash'), '');
    }

    // POST /auth/auth/registerWarga
    public function registerWargaPost(): void
    {
        // Set header JSON untuk response AJAX
        header('Content-Type: application/json');

        // Validasi CSRF
        $csrfToken = $this->input('_csrf_token', '');
        if (!$this->session->validateCsrfToken($csrfToken)) {
            echo json_encode(['success' => false, 'message' => 'Token keamanan tidak valid.']);
            return;
        }

        // Ambil data dari form
        $username = trim(strtolower($this->input('username', '')));
        $email = trim($this->input('email', ''));
        $password = $this->input('password', '');
        $passwordConfirm = $this->input('password_confirm', '');
        $namaLengkap = trim($this->input('nama_lengkap', ''));
        $nik = trim($this->input('nik', ''));
        $noKK = trim($this->input('no_kk', ''));
        $alamat = trim($this->input('alamat', ''));
        $rt = trim($this->input('rt', ''));
        $rw = trim($this->input('rw', ''));
        $tanggalLahir = $this->input('tanggal_lahir', '');
        $jenisKelamin = $this->input('jenis_kelamin', 'L');
        $agama = $this->input('agama', 'Islam');
        $pekerjaan = trim($this->input('pekerjaan', ''));

        // Validasi input
        $errors = [];

        // Validasi Username
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username minimal 3 karakter.';
        }
        if (!preg_match('/^[a-z0-9_]+$/', $username)) {
            $errors[] = 'Username hanya boleh huruf kecil, angka, dan underscore.';
        }

        // Validasi Email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        // Validasi Password
        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Password dan konfirmasi password tidak cocok.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf kapital.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung angka.';
        }

        // Validasi NIK
        if (empty($nik) || strlen($nik) !== 16 || !preg_match('/^[0-9]+$/', $nik)) {
            $errors[] = 'NIK harus 16 digit angka.';
        }

        // Validasi No KK
        if (empty($noKK) || strlen($noKK) !== 16 || !preg_match('/^[0-9]+$/', $noKK)) {
            $errors[] = 'Nomor KK harus 16 digit angka.';
        }

        // Validasi Nama Lengkap
        if (empty($namaLengkap)) {
            $errors[] = 'Nama lengkap wajib diisi.';
        }

        // Validasi Alamat
        if (empty($alamat)) {
            $errors[] = 'Alamat wajib diisi.';
        }

        // Validasi RT/RW
        if (empty($rt) || empty($rw)) {
            $errors[] = 'RT dan RW wajib diisi.';
        }

        // Validasi Tanggal Lahir
        if (empty($tanggalLahir)) {
            $errors[] = 'Tanggal lahir wajib diisi.';
        }

        // Validasi Pekerjaan
        if (empty($pekerjaan)) {
            $errors[] = 'Pekerjaan wajib diisi.';
        }

        // Jika ada error, kirim response error
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
            return;
        }

        // Cek username duplikat
        if ($this->userRepo->findByUsername($username)) {
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan.']);
            return;
        }

        // Cek email duplikat
        if ($this->userRepo->findByEmail($email)) {
            echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar.']);
            return;
        }

        // Cek NIK duplikat - menggunakan method findByNik yang sudah ada
        if ($this->pendudukRepo->findByNik($nik)) {
            echo json_encode(['success' => false, 'message' => 'NIK sudah terdaftar.']);
            return;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        // Mulai transaction
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // 1. Simpan user (role_id = 1 untuk warga)
            $userId = $this->userRepo->create([
                'role_id' => 1, // Warga
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'email_verified' => 1,
                'is_active' => 1
            ]);

            if (!$userId) {
                throw new \Exception('Gagal menyimpan data user.');
            }

            // 2. Simpan data penduduk
            $pendudukId = $this->pendudukRepo->create([
                'user_id' => $userId,
                'nik' => $nik,
                'no_kk' => $noKK,
                'nama' => $namaLengkap,
                'tempat_lahir' => '-',
                'tanggal_lahir' => $tanggalLahir,
                'jenis_kelamin' => $jenisKelamin,
                'agama' => $agama,
                'status_kawin' => 'Belum Kawin',
                'pekerjaan' => $pekerjaan,
                'pendidikan' => 'Tidak/Belum Sekolah',
                'alamat' => $alamat,
                'rt' => $rt,
                'rw' => $rw,
                'dusun' => '-',
                'desa' => DESA_NAMA,
                'kecamatan' => DESA_KEC,
                'kabupaten' => DESA_KAB,
                'provinsi' => DESA_PROV,
                'status_penduduk' => 'Tetap',
                'kewarganegaraan' => 'WNI'
            ]);

            if (!$pendudukId) {
                throw new \Exception('Gagal menyimpan data penduduk.');
            }

            // Commit transaction
            $db->commit();

            // Log aktivitas
            $this->logActivity($userId, 'registrasi_warga', 'auth', "Registrasi warga baru: {$username} (NIK: {$nik})");

            echo json_encode([
                'success' => true,
                'message' => 'Akun warga berhasil didaftarkan! Silakan login.',
                'data' => [
                    'id' => $userId,
                    'username' => $username,
                    'email' => $email
                ]
            ]);

        } catch (\Exception $e) {
            $db->rollback();
            echo json_encode(['success' => false, 'message' => 'Gagal mendaftar: ' . $e->getMessage()]);
        }
    }

    // GET|POST /auth/logout
    public function logout(): void
    {
        $this->authService->logout();
        $this->flash('success', 'Anda telah berhasil keluar dari sistem.');
        $this->redirect('login');
    }

    private function getDashboardUrl(): string
    {
        $role = $this->authRole();
        return match($role) {
            'warga'       => 'warga/dashboard',
            'admin'       => 'admin/dashboard',
            'kepala_desa' => 'kepaladesa/dashboard',
            'superadmin'  => 'superadmin/dashboard',
            default       => 'login',
        };
    }

    /**
     * Log aktivitas ke database
     */
    private function logActivity(int $userId, string $action, string $module, string $description): void
    {
        $db = Database::getInstance();
        $db->insert('log_aktivitas', [
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    }
}