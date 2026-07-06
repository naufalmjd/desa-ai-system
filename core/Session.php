<?php

declare(strict_types=1);

namespace Core;

/**
 * Session — Wrapper PHP session dengan keamanan tambahan.
 */
final class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        // Konfigurasi keamanan session
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure',  APP_ENV === 'production' ? '1' : '0');
        ini_set('session.cookie_samesite','Strict');
        ini_set('session.use_strict_mode','1');
        ini_set('session.gc_maxlifetime', (string)SESSION_LIFETIME);

        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => APP_ENV === 'production',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();
        self::$started = true;

        // Regenerate ID setiap 30 menit untuk mencegah session fixation
        if (!isset($_SESSION['_last_regen']) || time() - $_SESSION['_last_regen'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_last_regen'] = time();
        }
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        session_unset();
        session_destroy();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        self::$started = false;
    }

    /** Simpan data autentikasi user ke session */
    public function login(array $user): void
    {
        $this->set('user', [
            'id'       => (int)$user['id'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => $user['role_name'],
            'role_id'  => (int)$user['role_id'],
            'nama'     => $user['nama'] ?? $user['username'],
            'login_at' => time(),
        ]);
        session_regenerate_id(true);
    }

    public function isLoggedIn(): bool
    {
        return $this->has('user') && isset($this->get('user')['id']);
    }

    /** Generate CSRF token dan simpan di session */
    public function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(CSRF_TOKEN_LEN / 2));
        $this->set('csrf_token', $token);
        $this->set('csrf_expire', time() + CSRF_EXPIRE);
        return $token;
    }

    /** Validasi CSRF token */
    public function validateCsrfToken(string $token): bool
    {
        $stored  = $this->get('csrf_token');
        $expires = (int)$this->get('csrf_expire', 0);

        if (!$stored || !$token) return false;
        if (time() > $expires) return false;

        return hash_equals($stored, $token);
    }
}
