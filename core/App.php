<?php

declare(strict_types=1);

namespace Core;

use Middleware\AuthMiddleware;
use Middleware\RBACMiddleware;
use Middleware\CsrfMiddleware;

/**
 * App — Front Controller & Router
 *
 * URL pattern: /module/controller/action/...params
 *
 * Contoh:
 *  /warga/dashboard          → Controller\Warga\DashboardController::index()
 *  /admin/penduduk/store     → Controller\Admin\PendudukController::store()
 *  /kepaladesa/surat/approve/5 → Controller\Kepaladesa\SuratController::approve(5)
 */
final class App
{
    private string $module     = '';
    private string $controller = 'Auth\AuthController';
    private string $action     = 'login';
    private array  $params     = [];

    public function run(): void
    {
        // Mulai session
        Session::start();

        // Parse URL
        $this->parseUrl();

        // Global middleware
        $this->applyGlobalMiddleware();

        // Dispatch
        $this->dispatch();
    }

    // ── URL Parser ────────────────────────────────────────────────────────

    private function parseUrl(): void
    {
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';
        $base   = parse_url(APP_URL, PHP_URL_PATH) ?? '';
        $path   = urldecode(parse_url($uri, PHP_URL_PATH) ?? '/');

        // Hapus base path
        if ($base && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = trim($path, '/');

        if ($path === '' || $path === 'login') {
            $this->controller = 'Auth\AuthController';
            $this->action     = 'login';
            return;
        }

        $segments = explode('/', $path);

        $module = strtolower($segments[0] ?? '');
        $allowedModules = ['warga', 'admin', 'kepaladesa', 'superadmin', 'auth', 'api'];

        if ($module === 'auth' && ($segments[1] ?? '') === 'logout') {
            $this->module     = 'auth';
            $this->controller = 'Auth\AuthController';
            $this->action     = 'logout';
            $this->params     = [];
            return;
        }

        if (in_array($module, $allowedModules, true)) {
            $this->module = $module;
            $ctrl = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($segments[1] ?? 'dashboard'))));
            $ns   = match($module) {
                'warga'      => 'Warga',
                'admin'      => 'Admin',
                'kepaladesa' => 'Kepaladesa',
                'superadmin' => 'Superadmin',
                'api'        => 'Api',
                default      => 'Auth',
            };
            $this->controller = "$ns\\{$ctrl}Controller";
            $this->action     = $segments[2] ?? 'index';
            $this->params     = array_slice($segments, 3);
        } else {
            // Default: auth
            $this->controller = 'Auth\AuthController';
            $this->action     = $segments[0] ?? 'login';
            $this->params     = array_slice($segments, 1);
        }
    }

    // ── Middleware ────────────────────────────────────────────────────────

    private function applyGlobalMiddleware(): void
    {
        // CSRF hanya untuk POST, PUT, DELETE
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD', 'OPTIONS'], true)) {
            (new CsrfMiddleware())->handle();
        }
    }

    // ── Dispatch ──────────────────────────────────────────────────────────

    private function dispatch(): void
    {
        $ctrlClass = 'Controller\\' . $this->controller;

        if (!class_exists($ctrlClass)) {
            $this->abort(404);
            return;
        }

        $instance = new $ctrlClass();

        if (!method_exists($instance, $this->action)) {
            $this->abort(404);
            return;
        }

        // Auth & RBAC middleware
        $publicRoutes = ['Auth\AuthController::login', 'Auth\AuthController::loginPost', 'Auth\AuthController::logout'];
        $currentRoute = $this->controller . '::' . $this->action;

        if (!in_array($currentRoute, $publicRoutes, true)) {
            // Cek autentikasi
            (new AuthMiddleware())->handle();

            // Cek RBAC berdasarkan modul
            (new RBACMiddleware($this->module))->handle();
        }

        // Jalankan action dengan params
        call_user_func_array([$instance, $this->action], $this->params);
    }

    private function abort(int $code): void
    {
        http_response_code($code);
        $view = VIEW_PATH . "/errors/$code.php";
        if (is_file($view)) {
            require $view;
        } else {
            echo "<h1>Error $code</h1>";
        }
    }
}
