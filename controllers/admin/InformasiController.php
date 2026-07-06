<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;

/**
 * InformasiController — Mengelola berita, pengumuman, dan bansos oleh Admin.
 */
final class InformasiController extends Controller
{
    public function index(): void
    {
        $berita = $this->db->fetchAll(
            "SELECT * FROM berita WHERE deleted_at IS NULL ORDER BY created_at DESC"
        );
        $bantuan = $this->db->fetchAll(
            "SELECT * FROM bantuan_sosial ORDER BY created_at DESC"
        );
        $user = $this->auth();
        $flash = $this->getFlash();
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('admin/informasi/index', compact('berita', 'bantuan', 'user', 'flash', 'csrfToken'), 'admin');
    }

    public function storeBerita(): void
    {
        $user = $this->auth();
        
        $judul    = trim($this->input('judul', ''));
        $kategori = trim($this->input('kategori', 'berita'));
        $status   = trim($this->input('status', 'draft'));
        $konten   = trim($this->input('konten', ''));

        if (!$judul || !$konten) {
            $this->flash('danger', 'Judul dan isi konten berita wajib diisi.');
            $this->redirect('admin/informasi');
        }

        // Generate clean unique slug
        $baseSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $judul));
        $baseSlug = trim($baseSlug, '-');
        $slug     = $baseSlug;
        $i        = 1;
        
        while ($this->db->fetchColumn('SELECT COUNT(*) FROM berita WHERE slug = ?', [$slug]) > 0) {
            $slug = $baseSlug . '-' . $i++;
        }

        // Generate excerpt
        $excerpt = mb_strimwidth(strip_tags($konten), 0, 150, '...');

        // Handle File Uploads (Gambar/Thumbnail & Dokumen Lampiran)
        $thumbnail = null;
        $filePath = null;
        $dir = UPLOAD_PATH . '/berita';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 1. Gambar/Thumbnail
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['thumbnail'];
            $mime = $file['type'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($mime, $allowedTypes, true)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $thumbnail = 'thumb_' . bin2hex(random_bytes(8)) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $dir . '/' . $thumbnail);
            }
        }

        // 2. Dokumen Berita Full (PDF, Word, Excel)
        if (isset($_FILES['file_berita']) && $_FILES['file_berita']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['file_berita'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
            if (in_array($ext, $allowedExtensions, true)) {
                $filePath = 'doc_' . bin2hex(random_bytes(8)) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $dir . '/' . $filePath);
            }
        }

        $this->db->query(
            'INSERT INTO berita (user_id, judul, slug, kategori, konten, excerpt, thumbnail, file_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$user['id'], $judul, $slug, $kategori, $konten, $excerpt, $thumbnail, $filePath, $status]
        );

        $this->flash('success', 'Berita/Pengumuman baru berhasil disimpan dan dipublikasikan.');
        $this->redirect('admin/informasi');
    }

    public function storeBansos(): void
    {
        $this->auth();

        $nama      = trim($this->input('nama', ''));
        $sasaran   = trim($this->input('sasaran', ''));
        $besaran   = trim($this->input('besaran', ''));
        $periode   = trim($this->input('periode', ''));
        $status    = trim($this->input('status', 'aktif'));
        $syaratRaw = trim($this->input('syarat', ''));
        $deskripsi = trim($this->input('deskripsi', ''));

        if (!$nama || !$sasaran || !$besaran || !$deskripsi) {
            $this->flash('danger', 'Nama bansos, sasaran, besaran bantuan, dan deskripsi wajib diisi.');
            $this->redirect('admin/informasi');
        }

        // Parse syarat lines to JSON array
        $syaratLines = explode("\n", $syaratRaw);
        $syaratArr   = array_values(array_filter(array_map('trim', $syaratLines)));
        $syaratJson  = json_encode($syaratArr);

        $this->db->query(
            'INSERT INTO bantuan_sosial (nama, deskripsi, besaran, sasaran, syarat, periode, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$nama, $deskripsi, $besaran, $sasaran, $syaratJson, $periode, $status]
        );

        $this->flash('success', 'Program Bantuan Sosial (Bansos) baru berhasil disimpan.');
        $this->redirect('admin/informasi');
    }
}
