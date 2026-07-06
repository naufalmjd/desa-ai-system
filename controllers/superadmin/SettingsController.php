<?php

declare(strict_types=1);

namespace Controller\Superadmin;

use Core\Controller;

/**
 * SettingsController — Kelola Konfigurasi Website (Super Admin)
 */
final class SettingsController extends Controller
{
    // GET /superadmin/settings
    public function index(): void
    {
        $settingsFile = ROOT_PATH . '/config/settings.json';
        $settings = [];
        if (is_file($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true) ?: [];
        }

        // Fill defaults if empty
        $settings['app_name'] = $settings['app_name'] ?? 'SIAP-Desa';
        $settings['app_full'] = $settings['app_full'] ?? 'Sistem Pelayanan Administrasi Desa Berbasis AI';
        $settings['desa_nama'] = $settings['desa_nama'] ?? 'Desa Sukamaju';
        $settings['desa_kec'] = $settings['desa_kec'] ?? 'Tumpang';
        $settings['desa_kab'] = $settings['desa_kab'] ?? 'Kabupaten Malang';
        $settings['desa_prov'] = $settings['desa_prov'] ?? 'Jawa Timur';
        $settings['ai_server_url'] = $settings['ai_server_url'] ?? 'http://127.0.0.1:8000';
        $settings['ai_timeout'] = $settings['ai_timeout'] ?? 30;

        $flash = $this->getFlash();
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('superadmin/settings/index', compact('settings', 'flash', 'csrfToken'), 'superadmin');
    }

    // POST /superadmin/settings/update
    public function update(): void
    {
        $appName = trim($this->clean($this->input('app_name', '')));
        $appFull = trim($this->clean($this->input('app_full', '')));
        $desaNama = trim($this->clean($this->input('desa_nama', '')));
        $desaKec = trim($this->clean($this->input('desa_kec', '')));
        $desaKab = trim($this->clean($this->input('desa_kab', '')));
        $desaProv = trim($this->clean($this->input('desa_prov', '')));
        $aiServerUrl = trim($this->clean($this->input('ai_server_url', '')));
        $aiTimeout = (int)$this->input('ai_timeout', 30);

        if (!$appName || !$appFull || !$desaNama || !$desaKec || !$desaKab || !$desaProv || !$aiServerUrl) {
            $this->flash('danger', 'Semua field wajib diisi.');
            $this->redirect('superadmin/settings');
        }

        $settings = [
            'app_name' => $appName,
            'app_full' => $appFull,
            'desa_nama' => $desaNama,
            'desa_kec' => $desaKec,
            'desa_kab' => $desaKab,
            'desa_prov' => $desaProv,
            'ai_server_url' => $aiServerUrl,
            'ai_timeout' => $aiTimeout,
        ];

        $settingsFile = ROOT_PATH . '/config/settings.json';
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        $this->logActivity('Memperbarui konfigurasi website');
        $this->flash('success', 'Konfigurasi website berhasil diperbarui.');
        $this->redirect('superadmin/settings');
    }

    private function logActivity(string $desc): void
    {
        $this->db->insert('log_aktivitas', [
            'user_id'     => $this->authId(),
            'action'      => 'ubah_konfigurasi',
            'module'      => 'superadmin',
            'description' => $desc,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
