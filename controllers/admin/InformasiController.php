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

        $this->render('admin/informasi/index', compact('berita', 'bantuan', 'user', 'flash'), 'admin');
    }
}
