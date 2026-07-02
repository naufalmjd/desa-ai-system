<?php

declare(strict_types=1);

namespace Controller\Auth;

use Core\{Controller, Database, Session};
use Service\AuthService;

/**
 * AuthController — Login, Logout, Register.
 */
final class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService($this->db, $this->session);
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

        $this->render('auth/login', compact('csrfToken', 'flash'), '');
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
            default       => 'login',
        };
    }
}
