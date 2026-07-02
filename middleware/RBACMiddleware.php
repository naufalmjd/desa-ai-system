<?php

declare(strict_types=1);

namespace Middleware;

use Core\Session;

/**
 * RBACMiddleware — Role-Based Access Control.
 *
 * Aturan akses:
 *   /warga/*      → hanya role 'warga'
 *   /admin/*      → hanya role 'admin'
 *   /kepaladesa/* → hanya role 'kepala_desa'
 */
final class RBACMiddleware
{
    /** Peta modul URL → role yang diizinkan */
    private const ROLE_MAP = [
        'warga'      => ['warga'],
        'admin'      => ['admin'],
        'kepaladesa' => ['kepala_desa'],
    ];

    public function __construct(private string $module) {}

    public function handle(): void
    {
        if ($this->module === '' || $this->module === 'auth') {
            return; // Tidak perlu cek RBAC untuk modul auth
        }

        $session   = new Session();
        $user      = $session->get('user');
        $userRole  = $user['role'] ?? '';

        $allowedRoles = self::ROLE_MAP[$this->module] ?? [];

        if (empty($allowedRoles) || !in_array($userRole, $allowedRoles, true)) {
            $this->forbidden($userRole);
        }
    }

    private function forbidden(string $userRole): never
    {
        http_response_code(403);

        // Tentukan dashboard yang seharusnya diakses user
        $redirectUrl = match($userRole) {
            'warga'       => APP_URL . '/warga/dashboard',
            'admin'       => APP_URL . '/admin/dashboard',
            'kepala_desa' => APP_URL . '/kepaladesa/dashboard',
            default       => APP_URL . '/login',
        };

        require VIEW_PATH . '/errors/403.php';
        exit;
    }
}
