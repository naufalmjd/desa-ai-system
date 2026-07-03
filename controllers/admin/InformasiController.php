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

        $this->db->query(
            'INSERT INTO berita (user_id, judul, slug, kategori, konten, excerpt, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$user['id'], $judul, $slug, $kategori, $konten, $excerpt, $status]
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
