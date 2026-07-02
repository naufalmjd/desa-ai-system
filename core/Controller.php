<?php

declare(strict_types=1);

namespace Core;

/**
 * Controller — Base controller dengan helper render, redirect, json, dll.
 */
abstract class Controller
{
    protected Database $db;
    protected Session  $session;

    public function __construct()
    {
        $this->db      = Database::getInstance();
        $this->session = new Session();
    }

    // ── View Renderer ─────────────────────────────────────────────────────

    protected function render(string $view, array $data = [], string $layout = ''): void
    {
        // Extract data ke scope view
        extract($data, EXTR_SKIP);

        // Tangkap output view
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (is_file($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
        } else {
            // Render a beautiful, responsive "Under Construction" page inside the layout
            $pageName = ucwords(str_replace(['/', '_', '.'], ' ', $view));
            ob_start();
            ?>
            <div class="container py-5 text-center" data-aos="fade-up">
                <div class="card border-0 shadow-sm p-5 rounded-4 bg-white position-relative overflow-hidden">
                    <div class="position-absolute start-0 top-0 w-100 h-2 bg-primary"></div>
                    <div class="mb-4">
                        <span class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle" style="width: 80px; height: 80px;">
                            <i class="bi bi-cone-striped" style="font-size: 2.5rem;"></i>
                        </span>
                    </div>
                    <h2 class="fw-black text-dark mb-2">Fitur Sedang Dikembangkan</h2>
                    <p class="text-muted mx-auto mb-4" style="max-width: 480px;">
                        Halaman <strong><?= htmlspecialchars($pageName) ?></strong> saat ini sedang dalam proses pengembangan oleh tim IT <?= DESA_NAMA ?>. Kami akan segera menghadirkannya untuk Anda.
                    </p>
                    <div class="progress mx-auto mb-4" style="height: 8px; max-width: 320px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 65%"></div>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <button onclick="history.back()" class="btn btn-light px-4 py-2" style="border-radius: 10px;">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </button>
                        <a href="<?= APP_URL ?>/warga/dashboard" class="btn btn-primary px-4 py-2" style="border-radius: 10px;">
                            <i class="bi bi-house-door me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
            <?php
            $content = ob_get_clean();
        }

        // Render layout jika ada dan belum dirender di dalam view file
        $alreadyHasLayout = str_contains($content, '<!DOCTYPE html>') || str_contains($content, '<html');
        if ($layout && !$alreadyHasLayout) {
            $layoutFile = VIEW_PATH . '/layouts/' . $layout . '.php';
            if (!is_file($layoutFile)) {
                throw new \RuntimeException("Layout tidak ditemukan: $layoutFile");
            }
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    // ── JSON Response ─────────────────────────────────────────────────────

    protected function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    protected function jsonSuccess(mixed $data = null, string $message = 'Berhasil'): never
    {
        $this->json(['success' => true, 'message' => $message, 'data' => $data]);
    }

    protected function jsonError(string $message, int $code = 400, mixed $errors = null): never
    {
        $this->json(['success' => false, 'message' => $message, 'errors' => $errors], $code);
    }

    // ── Redirect ──────────────────────────────────────────────────────────

    protected function redirect(string $url): never
    {
        $full = str_starts_with($url, 'http') ? $url : APP_URL . '/' . ltrim($url, '/');
        header("Location: $full", true, 302);
        exit;
    }

    protected function redirectBack(): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        $this->redirect($ref);
    }

    // ── Request helpers ───────────────────────────────────────────────────

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function inputAll(): array
    {
        return array_merge($_GET, $_POST);
    }

    protected function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
    protected function isGet(): bool  { return $_SERVER['REQUEST_METHOD'] === 'GET'; }
    protected function isAjax(): bool { return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'; }

    // ── XSS Filter ────────────────────────────────────────────────────────

    protected function clean(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function cleanArray(array $data): array
    {
        return array_map(fn($v) => is_string($v) ? $this->clean($v) : $v, $data);
    }

    // ── Flash messages ────────────────────────────────────────────────────

    protected function flash(string $type, string $message): void
    {
        $this->session->set('flash', ['type' => $type, 'message' => $message]);
    }

    protected function getFlash(): ?array
    {
        $flash = $this->session->get('flash');
        $this->session->remove('flash');
        return $flash;
    }

    // ── Auth user ─────────────────────────────────────────────────────────

    protected function auth(): ?array
    {
        return $this->session->get('user');
    }

    protected function authId(): ?int
    {
        return $this->auth()['id'] ?? null;
    }

    protected function authRole(): ?string
    {
        return $this->auth()['role'] ?? null;
    }

    // ── Abort ─────────────────────────────────────────────────────────────

    protected function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        $view = VIEW_PATH . "/errors/$code.php";
        if (is_file($view)) {
            extract(['message' => $message]);
            require $view;
        } else {
            echo "<h1>Error $code</h1><p>$message</p>";
        }
        exit;
    }
}
