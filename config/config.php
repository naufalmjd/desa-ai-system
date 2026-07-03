<?php
/**
 * Konfigurasi Utama Aplikasi
 * Sistem Pelayanan Administrasi Desa — v2.0
 */

declare(strict_types=1);

// ─── Environment ──────────────────────────────────────────────────────────────
define('APP_ENV',     getenv('APP_ENV')  ?: 'development');  // production | development
define('APP_DEBUG',   APP_ENV === 'development');
define('APP_VERSION', '2.0.0');

// ─── Paths ───────────────────────────────────────────────────────────────────
define('ROOT_PATH',   dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH',   ROOT_PATH . '/core');
define('CTRL_PATH',   ROOT_PATH . '/controllers');
define('REPO_PATH',   ROOT_PATH . '/repositories');
define('SVC_PATH',    ROOT_PATH . '/services');
define('MDL_PATH',    ROOT_PATH . '/models');
define('VIEW_PATH',   ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// ─── App ─────────────────────────────────────────────────────────────────────
define('APP_NAME',    'SIAP-Desa');
define('APP_FULL',    'Sistem Pelayanan Administrasi Desa Berbasis AI');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('APP_URL',     getenv('APP_URL') ?: $protocol . $host . '/desa-ai-system');
define('DESA_NAMA',   'Desa Sukamaju');
define('DESA_KEC',    'Tumpang');
define('DESA_KAB',    'Kabupaten Malang');
define('DESA_PROV',   'Jawa Timur');

// ─── Session ─────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'DESAAI_SESS');
define('SESSION_LIFETIME', 7200);         // 2 jam
define('REMEMBER_LIFETIME', 2592000);     // 30 hari
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCK_MINUTES', 15);

// ─── Security ────────────────────────────────────────────────────────────────
define('BCRYPT_COST',    12);
define('CSRF_TOKEN_LEN', 64);
define('CSRF_EXPIRE',    3600);           // 1 jam

// ─── Upload ──────────────────────────────────────────────────────────────────
define('MAX_FILE_SIZE',   5 * 1024 * 1024);   // 5 MB
define('ALLOWED_IMAGE',   ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_DOC',     ['application/pdf', 'image/jpeg', 'image/png']);
define('ALLOWED_VIDEO',   ['video/mp4', 'video/mpeg', 'video/quicktime']);

// ─── Pagination ──────────────────────────────────────────────────────────────
define('PER_PAGE', 15);

// ─── AI Server ───────────────────────────────────────────────────────────────
define('AI_SERVER_URL',  getenv('AI_SERVER_URL')  ?: 'http://127.0.0.1:8000');
define('AI_SERVER_KEY',  getenv('AI_SERVER_KEY')  ?: 'your-api-key-here');
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'your-gemini-key-here');
define('AI_TIMEOUT',     30);             // detik

// ─── Error Handling ──────────────────────────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/logs/php_errors.log');
}

// ─── Timezone ────────────────────────────────────────────────────────────────
date_default_timezone_set('Asia/Jakarta');

// ─── Autoloader ──────────────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $map = [
        'Core\\'         => CORE_PATH,
        'Middleware\\'   => ROOT_PATH . '/middleware',
        'Repository\\'  => REPO_PATH,
        'Service\\'     => SVC_PATH,
        'Controller\\'  => CTRL_PATH,
        'Model\\'       => MDL_PATH,
    ];

    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file     = $dir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
            if (is_file($file)) {
                require_once $file;
                return;
            }
        }
    }
});
