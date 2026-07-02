<?php

declare(strict_types=1);

namespace Controller\Warga;

use Core\Controller;

/**
 * InformasiController — Mengakses wawasan, berita, dan pengumuman desa.
 */
final class InformasiController extends Controller
{
    public function index(): void
    {
        $berita = $this->db->fetchAll(
            "SELECT * FROM berita WHERE status = 'publish' AND deleted_at IS NULL ORDER BY created_at DESC"
        );
        $bantuan = $this->db->fetchAll(
            "SELECT * FROM bantuan_sosial ORDER BY created_at DESC"
        );
        $user = $this->auth();

        $this->render('warga/informasi/index', compact('berita', 'bantuan', 'user'), 'warga');
    }
}
