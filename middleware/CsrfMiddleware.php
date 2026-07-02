<?php

declare(strict_types=1);

namespace Middleware;

use Core\Session;

/**
 * CsrfMiddleware — Validasi CSRF token pada request POST/PUT/DELETE.
 */
final class CsrfMiddleware
{
    public function handle(): void
    {
        $session = new Session();

        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$session->validateCsrfToken($token)) {
            http_response_code(419);

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'CSRF token tidak valid atau sudah kedaluwarsa.']);
            } else {
                $session->set('flash', ['type' => 'danger', 'message' => 'Sesi keamanan tidak valid. Silakan muat ulang halaman.']);
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? APP_URL), true, 302);
            }
            exit;
        }

        // Validation successful
    }
}
