<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;

/**
 * SettingsController — Mengelola konfigurasi portal beranda depan oleh Admin.
 */
final class SettingsController extends Controller
{
    private string $settingsPath;

    public function __construct()
    {
        parent::__construct();
        $this->settingsPath = dirname(__DIR__, 2) . '/config/landing_settings.json';
    }

    public function index(): void
    {
        $settings = $this->loadSettings();
        $user = $this->auth();
        $flash = $this->getFlash();
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('admin/settings/index', compact('settings', 'user', 'flash', 'csrfToken'), 'admin');
    }

    public function save(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/settings');
        }

        $data = [
            'announcement_title'   => trim((string)$this->input('announcement_title')),
            'announcement_date'    => trim((string)$this->input('announcement_date')),
            'announcement_content' => trim((string)$this->input('announcement_content')),
            'stat_penduduk'        => trim((string)$this->input('stat_penduduk')),
            'stat_luas'            => trim((string)$this->input('stat_luas')),
            'stat_wilayah'         => trim((string)$this->input('stat_wilayah')),
            'ambulance_phone'      => trim((string)$this->input('ambulance_phone')),
            'ambulance_description'=> trim((string)$this->input('ambulance_description')),
            'contact_address'      => trim((string)$this->input('contact_address')),
            'contact_email'        => trim((string)$this->input('contact_email')),
            'contact_phone'        => trim((string)$this->input('contact_phone')),
            'contact_maps'         => trim((string)$this->input('contact_maps')),
        ];

        // Format nomor telepon (08... -> 628...)
        $phone = $data['ambulance_phone'];
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        $data['ambulance_phone'] = preg_replace('/[^0-9]/', '', $phone);

        // Tulis data ke file JSON
        $dir = dirname($this->settingsPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_put_contents($this->settingsPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $this->flash('success', 'Pengaturan portal beranda berhasil disimpan!');
        } else {
            $this->flash('danger', 'Gagal menyimpan pengaturan.');
        }

        $this->redirect('admin/settings');
    }

    private function loadSettings(): array
    {
        $default = [
            'announcement_title'   => 'Pengumuman Penting Kepala Desa',
            'announcement_date'    => '04 Juli 2026',
            'announcement_content' => '"Dihimbau kepada seluruh ketua RT/RW dan warga desa untuk ikut berpartisipasi dalam kegiatan kerja bakti massal pembersihan saluran irigasi pada hari Minggu, 12 Juli 2026 jam 07:00 WIB guna menyambut musim tanam."',
            'stat_penduduk'        => '3,450',
            'stat_luas'            => '12.8 Km²',
            'stat_wilayah'         => '12 RT / 4 RW',
            'ambulance_phone'      => '628123456789',
            'ambulance_description'=> 'Butuh bantuan medis segera? Pesan Sopir Ambulans Desa langsung ke kontak WhatsApp admin siaga. Layanan aktif 24 jam bebas biaya bagi seluruh warga.',
            'contact_address'      => 'Jl. Raya Demokrasi No. 45, Kecamatan Sukamaju, Kabupaten Wonosobo, Jawa Tengah, 56361',
            'contact_email'        => 'info@Smartdesa.id · desa.sukamaju@gmail.com',
            'contact_phone'        => '+62 812-3456-789 (Humas Desa)',
            'contact_maps'         => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126438.33806407062!2d109.83151978250645!3d-7.3596720516644265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7aa1a91e57c6b9%3A0x4027a7b50a25610!2sWonosobo%20Regency%2C%20Central%20Java!5e0!3m2!1sen!2sid!4v1704200000000!5m2!1sen!2sid',
        ];

        if (is_file($this->settingsPath)) {
            $saved = json_decode(file_get_contents($this->settingsPath), true);
            if (is_array($saved)) {
                return array_merge($default, $saved);
            }
        }
        return $default;
    }
}