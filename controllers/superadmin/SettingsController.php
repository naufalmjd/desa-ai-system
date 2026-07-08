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
        $settings['landing_bg_image'] = $settings['landing_bg_image'] ?? '';
        $settings['landing_about_title'] = $settings['landing_about_title'] ?? 'Tentang Portal';
        $settings['landing_about_desc1'] = $settings['landing_about_desc1'] ?? 'Portal Sistem Informasi **SmartDesa.id** dirancang untuk mempermudah koordinasi antara perangkat desa dan masyarakat. Dengan integrasi teknologi informasi, pengurusan administrasi surat-menyurat, pemantauan logistik desa, hingga penanganan darurat ambulans kini dapat diakses kapan saja dan di mana saja.';
        $settings['landing_about_desc2'] = $settings['landing_about_desc2'] ?? 'Kami berkomitmen memberikan keterbukaan informasi publik dan akuntabilitas anggaran desa guna mendukung terwujudnya konsep smart village di Indonesia.';

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
        $landingAboutTitle = trim($this->clean($this->input('landing_about_title', '')));
        $landingAboutDesc1 = trim($this->clean($this->input('landing_about_desc1', '')));
        $landingAboutDesc2 = trim($this->clean($this->input('landing_about_desc2', '')));

        if (!$appName || !$appFull || !$desaNama || !$desaKec || !$desaKab || !$desaProv || !$aiServerUrl || !$landingAboutTitle || !$landingAboutDesc1) {
            $this->flash('danger', 'Semua field wajib diisi.');
            $this->redirect('superadmin/settings');
        }

        $settingsFile = ROOT_PATH . '/config/settings.json';
        $settings = [];
        if (is_file($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true) ?: [];
        }

        // Handle file upload
        $landingImage = $settings['landing_image'] ?? '';
        if (isset($_FILES['landing_image']) && $_FILES['landing_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['landing_image'];
            if ($file['size'] > MAX_FILE_SIZE) {
                $this->flash('danger', 'Ukuran gambar maksimal 5MB.');
                $this->redirect('superadmin/settings');
            }
            if (!in_array($file['type'], ALLOWED_IMAGE, true)) {
                $this->flash('danger', 'Format gambar harus berupa JPG, PNG, atau WEBP.');
                $this->redirect('superadmin/settings');
            }

            $dir = UPLOAD_PATH;
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'landing_' . time() . '.' . $ext;
            $dest = $dir . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // Delete old image if exists
                if ($landingImage && is_file($dir . '/' . $landingImage)) {
                    @unlink($dir . '/' . $landingImage);
                }
                $landingImage = $filename;
            }
        }

        // Handle Background Image Upload
        $landingBgImage = $settings['landing_bg_image'] ?? '';
        if (isset($_FILES['landing_bg_image']) && $_FILES['landing_bg_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['landing_bg_image'];
            if ($file['size'] > MAX_FILE_SIZE) {
                $this->flash('danger', 'Ukuran background gambar maksimal 5MB.');
                $this->redirect('superadmin/settings');
            }
            if (!in_array($file['type'], ALLOWED_IMAGE, true)) {
                $this->flash('danger', 'Format background gambar harus berupa JPG, PNG, atau WEBP.');
                $this->redirect('superadmin/settings');
            }

            $dir = UPLOAD_PATH;
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'landing_bg_' . time() . '.' . $ext;
            $dest = $dir . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // Delete old background if exists
                if ($landingBgImage && is_file($dir . '/' . $landingBgImage)) {
                    @unlink($dir . '/' . $landingBgImage);
                }
                $landingBgImage = $filename;
            }
        }

        $settings['app_name'] = $appName;
        $settings['app_full'] = $appFull;
        $settings['desa_nama'] = $desaNama;
        $settings['desa_kec'] = $desaKec;
        $settings['desa_kab'] = $desaKab;
        $settings['desa_prov'] = $desaProv;
        $settings['ai_server_url'] = $aiServerUrl;
        $settings['ai_timeout'] = $aiTimeout;
        $settings['landing_image'] = $landingImage;
        $settings['landing_bg_image'] = $landingBgImage;
        $settings['landing_about_title'] = $landingAboutTitle;
        $settings['landing_about_desc1'] = $landingAboutDesc1;
        $settings['landing_about_desc2'] = $landingAboutDesc2;

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
