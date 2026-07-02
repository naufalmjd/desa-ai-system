<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;
use Repository\{UserRepository, PendudukRepository};

/**
 * UserController (Admin) — Registrasi akun warga/pengguna baru.
 *
 * Membuat akun login (tabel users) sekaligus data kependudukan (tabel
 * penduduk) dalam satu transaksi, lalu menghubungkan keduanya lewat
 * kolom penduduk.user_id.
 */
final class UserController extends Controller
{
    private UserRepository     $userRepo;
    private PendudukRepository $pendudukRepo;

    public function __construct()
    {
        parent::__construct();
        $this->userRepo     = new UserRepository($this->db);
        $this->pendudukRepo = new PendudukRepository($this->db);
    }

    // GET /admin/user
    public function index(): void
    {
        $filters = [
            'role_id' => (int)$this->input('role_id', 0),
            'search'  => $this->clean($this->input('q', '')),
        ];
        $page   = max(1, (int)$this->input('page', 1));
        $result = $this->userRepo->findAll($filters, $page);
        $flash  = $this->getFlash();

        $this->render('admin/user/index', compact('result', 'filters', 'flash'), 'admin');
    }

    // GET /admin/user/create
    public function create(): void
    {
        $csrfToken = $this->session->generateCsrfToken();
        $this->render('admin/user/create', compact('csrfToken'), 'admin');
    }

    // POST /admin/user/store
    public function store(): void
    {
        $username = trim($this->clean($this->input('username', '')));
        $email    = trim($this->clean($this->input('email', '')));
        $password = (string)$this->input('password', '');
        $roleId   = (int)$this->input('role_id', 1); // default 1 = warga

        $nama          = trim($this->clean($this->input('nama', '')));
        $nik           = trim($this->clean($this->input('nik', '')));
        $noKk          = trim($this->clean($this->input('no_kk', '')));
        $tempatLahir   = trim($this->clean($this->input('tempat_lahir', '')));
        $tanggalLahir  = $this->input('tanggal_lahir', '');
        $jenisKelamin  = $this->input('jenis_kelamin', 'L');
        $agama         = $this->input('agama', 'Islam');
        $alamat        = trim($this->clean($this->input('alamat', '')));
        $rt            = trim($this->clean($this->input('rt', '')));
        $rw            = trim($this->clean($this->input('rw', '')));
        $noHp          = trim($this->clean($this->input('no_hp', '')));

        // ── Validasi dasar ──────────────────────────────────────────────
        if (!$username || !$email || !$password || !$nama || !$nik || !$noKk
            || !$tempatLahir || !$tanggalLahir || !$alamat || !$rt || !$rw) {
            $this->flash('danger', 'Semua kolom bertanda wajib harus diisi.');
            $this->redirect('admin/user/create');
        }

        if (strlen($nik) !== 16 || !ctype_digit($nik)) {
            $this->flash('danger', 'NIK harus terdiri dari 16 digit angka.');
            $this->redirect('admin/user/create');
        }

        if (strlen($password) < 8) {
            $this->flash('danger', 'Password minimal 8 karakter.');
            $this->redirect('admin/user/create');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('danger', 'Format email tidak valid.');
            $this->redirect('admin/user/create');
        }

        if ($this->userRepo->findByUsername($username)) {
            $this->flash('danger', 'Username sudah digunakan.');
            $this->redirect('admin/user/create');
        }
        if ($this->userRepo->findByEmail($email)) {
            $this->flash('danger', 'Email sudah terdaftar.');
            $this->redirect('admin/user/create');
        }
        if ($this->pendudukRepo->findByNik($nik)) {
            $this->flash('danger', 'NIK sudah terdaftar di data kependudukan.');
            $this->redirect('admin/user/create');
        }

        try {
            $this->db->transaction(function () use (
                $username, $email, $password, $roleId, $nama, $nik, $noKk,
                $tempatLahir, $tanggalLahir, $jenisKelamin, $agama,
                $alamat, $rt, $rw, $noHp
            ) {
                // 1) Buat data kependudukan
                $pendudukId = $this->pendudukRepo->create([
                    'nik'            => $nik,
                    'no_kk'          => $noKk,
                    'nama'           => $nama,
                    'tempat_lahir'   => $tempatLahir,
                    'tanggal_lahir'  => $tanggalLahir,
                    'jenis_kelamin'  => $jenisKelamin,
                    'agama'          => $agama,
                    'pekerjaan'      => '-',
                    'alamat'         => $alamat,
                    'rt'             => $rt,
                    'rw'             => $rw,
                    'desa'           => DESA_NAMA,
                    'kecamatan'      => DESA_KEC,
                    'kabupaten'      => DESA_KAB,
                    'provinsi'       => DESA_PROV,
                    'no_hp'          => $noHp,
                    'email'          => $email,
                    'status_penduduk'=> 'Tetap',
                ]);

                // 2) Buat akun login, terhubung ke data penduduk di atas
                $userId = $this->userRepo->create([
                    'role_id'  => $roleId ?: 1,
                    'username' => $username,
                    'email'    => $email,
                    'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]),
                    'is_active'=> 1,
                ]);

                // 3) Hubungkan penduduk.user_id -> users.id
                $this->pendudukRepo->update((int)$pendudukId, ['user_id' => $userId]);

                $this->db->insert('log_aktivitas', [
                    'user_id'     => $this->authId(),
                    'action'      => 'registrasi_akun',
                    'module'      => 'user',
                    'description' => "Mendaftarkan akun baru: $username ($nama)",
                    'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
                ]);
            });
        } catch (\Throwable $e) {
            $this->flash('danger', 'Gagal membuat akun: ' . $e->getMessage());
            $this->redirect('admin/user/create');
        }

        $this->flash('success', "Akun untuk \"$nama\" berhasil dibuat. Beritahu username & password ke warga bersangkutan.");
        $this->redirect('admin/user');
    }
}
