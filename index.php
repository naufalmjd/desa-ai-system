<?php

/**
 * ============================================================
 *  SIAP-DESA — Front Controller
 *  Sistem Pelayanan Administrasi Desa Berbasis AI
 *  PHP 8.3 | MVC | Repository Pattern | Service Layer | RBAC
 * ============================================================
 */

declare(strict_types=1);

// PHP 8.3 minimum
// if (PHP_VERSION_ID < 80300) {
//     http_response_code(500);
//     die('PHP 8.3+ required. Current: ' . PHP_VERSION);
// }

// ── Load Config ──────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/config.php';

// ── Bootstrap & Run ──────────────────────────────────────────────────────────
(new Core\App())->run();
