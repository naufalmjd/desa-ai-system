<?php

declare(strict_types=1);

namespace Middleware;

use Core\Session;

/**
 * AuthMiddleware — Cek apakah user sudah login.
 */
final class AuthMiddleware
{
    public function handle(): void
    {
        $session = new Session();

        if (!$session->isLoggedIn()) {
            // Simpan URL yang diminta agar bisa redirect setelah login
            $session->set('intended_url', $_SERVER['REQUEST_URI'] ?? '');
            $this->redirectToLogin();
        }

        // Cek session timeout
        $user     = $session->get('user');
        $loginAt  = $user['login_at'] ?? 0;

        if (time() - $loginAt > SESSION_LIFETIME) {
            $session->destroy();
            $session->set('flash', ['type' => 'warning', 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            $this->redirectToLogin();
        }
    }

    private function redirectToLogin(): never
    {
        header('Location: ' . APP_URL . '/login', true, 302);
        exit;
    }
}
