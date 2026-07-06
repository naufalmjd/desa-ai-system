<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;
use Repository\PendudukRepository;
use Repository\UserRepository;
use Throwable;

/**
 * ProfilController — Mengelola data profil admin.
 */
final class ProfilController extends Controller
{
    public function index(): void
    {
        // 1. PROSES POST DATA VIA AJAX
        if ($this->isPost()) {
            try {
                $userId = $this->authId();
                if (!$userId) {
                    $this->jsonError('Sesi Anda telah berakhir, silakan login kembali.', 401);
                }

                // Mengambil dan membersihkan input
                $nama     = $this->clean((string)$this->input('nama'));
                $username = $this->clean((string)$this->input('username'));
                $email    = $this->clean((string)$this->input('email'));
                $password = (string)$this->input('password');

                if (empty($nama) || empty($username) || empty($email)) {
                    $this->jsonError('Nama, Username, dan Email wajib diisi!');
                }

                // Cek format email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->jsonError('Format email tidak valid.');
                }

                $userRepo = new UserRepository($this->db);
                $pendudukRepo = new PendudukRepository($this->db);

                // Cek keunikan username
                $existingUserByUsername = $userRepo->findByUsername($username);
                if ($existingUserByUsername && (int)$existingUserByUsername['id'] !== $userId) {
                    $this->jsonError('Username sudah digunakan oleh orang lain.');
                }

                // Cek keunikan email
                $existingUserByEmail = $userRepo->findByEmail($email);
                if ($existingUserByEmail && (int)$existingUserByEmail['id'] !== $userId) {
                    $this->jsonError('Email sudah terdaftar.');
                }

                // Handle Upload Foto Profil
                $filename = null;
                if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['foto_profil'];

                    if ($file['size'] > MAX_FILE_SIZE) {
                        $this->jsonError('Ukuran foto profil maksimal 5MB.');
                    }

                    $mime = $file['type'];
                    if (!in_array($mime, ALLOWED_IMAGE, true)) {
                        $this->jsonError('Format file harus berupa JPEG, PNG, atau WEBP.');
                    }

                    $dir = UPLOAD_PATH . '/profil';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }

                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
                    $dest = $dir . '/' . $filename;

                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        // Hapus foto lama jika ada
                        $currentUser = $userRepo->findById($userId);
                        $oldPhoto = $currentUser['foto_profil'] ?? '';
                        if ($oldPhoto && is_file($dir . '/' . $oldPhoto)) {
                            @unlink($dir . '/' . $oldPhoto);
                        }
                    } else {
                        $filename = null;
                    }
                }

                // Update users table
                $userData = [
                    'username' => $username,
                    'email'    => $email,
                ];
                if (!empty($password)) {
                    if (strlen($password) < 6) {
                        $this->jsonError('Password minimal 6 karakter.');
                    }
                    $userData['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
                }
                if ($filename !== null) {
                    $userData['foto_profil'] = $filename;
                }

                $userRepo->update($userId, $userData);

                // Update penduduk table (nama, email)
                $penduduk = $pendudukRepo->findByUserId($userId);
                if ($penduduk) {
                    $pendudukRepo->update((int)$penduduk['id'], [
                        'nama'  => $nama,
                        'email' => $email,
                    ]);
                }

                // Update session
                $sessionUser = $this->session->get('user');
                $sessionUser['email'] = $email;
                $sessionUser['username'] = $username;
                $sessionUser['nama'] = $nama;
                if ($filename !== null) {
                    $sessionUser['foto_profil'] = $filename;
                }
                $this->session->set('user', $sessionUser);

                $this->jsonSuccess(null, 'Profil Anda berhasil diperbarui!');

            } catch (Throwable $t) {
                $this->jsonError('Terjadi kesalahan sistem: ' . $t->getMessage());
            }
        }

        // 2. LOGIKA BAWAAN ASLI (GET REQUEST)
        $userId = $this->authId();
        $pendudukRepo = new PendudukRepository($this->db);
        $penduduk = $pendudukRepo->findByUserId($userId);
        $user = $this->auth();
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('admin/profil/index', compact('penduduk', 'user', 'csrfToken'), 'admin');
    }
}
