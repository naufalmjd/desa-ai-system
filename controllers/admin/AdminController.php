<?php

class AdminController extends BaseController
{
    // ... method lain

    public function updateProfil()
    {
        // Cek CSRF (jika pakai)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/profil');
        }

        // Ambil data dari form
        $userId = $_SESSION['user_id'] ?? 0;
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validasi dasar
        $errors = [];
        if (empty($username)) $errors[] = 'Username harus diisi.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
        if (!empty($password) && $password !== $passwordConfirm) $errors[] = 'Password dan konfirmasi tidak cocok.';
        if (!empty($password) && strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

        if (!empty($errors)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => implode('<br>', $errors)];
            redirect('/admin/profil');
        }

        // Ambil data user saat ini
        $userModel = new User();
        $user = $userModel->find($userId);
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'User tidak ditemukan.'];
            redirect('/admin/profil');
        }

        // Siapkan data update
        $data = [
            'username' => $username,
            'email'    => $email,
        ];

        // Jika password diisi, hash dan update
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update ke database
        $updated = $userModel->update($userId, $data);

        if ($updated) {
            // Jika username berubah, update session juga
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profil berhasil diperbarui.'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui profil.'];
        }

        redirect('/admin/profil');
    }
}