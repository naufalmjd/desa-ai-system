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
$customSettingsFile = ROOT_PATH . '/config/settings.json';
$customSettings = [];
if (is_file($customSettingsFile)) {
    $customSettings = json_decode(file_get_contents($customSettingsFile), true) ?: [];
}

define('APP_NAME',    $customSettings['app_name'] ?? 'SIAP-Desa');
define('APP_FULL',    $customSettings['app_full'] ?? 'Sistem Pelayanan Administrasi Desa Berbasis AI');
// Deteksi Protokol secara Dinamis (Mendukung Proxy/Tunneling seperti Ngrok, Anti Gravity, dll.)
$protocol = 'http://';
if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
) {
    $protocol = 'https://';
}

// Deteksi Host secara Dinamis
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $forwardedHosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
    $host = trim($forwardedHosts[0]);
}

$baseDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
define('APP_URL', getenv('APP_URL') ?: $protocol . $host . $baseDir);
define('DESA_NAMA',   $customSettings['desa_nama'] ?? 'Desa Sukamaju');
define('DESA_KEC',    $customSettings['desa_kec'] ?? 'Tumpang');
define('DESA_KAB',    $customSettings['desa_kab'] ?? 'Kabupaten Malang');
define('DESA_PROV',   $customSettings['desa_prov'] ?? 'Jawa Timur');
define('LANDING_IMAGE', $customSettings['landing_image'] ?? '');
define('LANDING_BG_IMAGE', $customSettings['landing_bg_image'] ?? '');
define('LANDING_ABOUT_TITLE', $customSettings['landing_about_title'] ?? 'Tentang Portal');
define('LANDING_ABOUT_DESC1', $customSettings['landing_about_desc1'] ?? 'Portal Sistem Informasi **SmartDesa.id** dirancang untuk mempermudah koordinasi antara perangkat desa dan masyarakat. Dengan integrasi teknologi informasi, pengurusan administrasi surat-menyurat, pemantauan logistik desa, hingga penanganan darurat ambulans kini dapat diakses kapan saja dan di mana saja.');
define('LANDING_ABOUT_DESC2', $customSettings['landing_about_desc2'] ?? 'Kami berkomitmen memberikan keterbukaan informasi publik dan akuntabilitas anggaran desa guna mendukung terwujudnya konsep smart village di Indonesia.');

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
define('AI_SERVER_URL',  $customSettings['ai_server_url'] ?? 'http://127.0.0.1:8000');
define('AI_SERVER_KEY',  'isi_api_key_anda');
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'your-gemini-key-here');
define('AI_TIMEOUT',     isset($customSettings['ai_timeout']) ? (int)$customSettings['ai_timeout'] : 60);

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
