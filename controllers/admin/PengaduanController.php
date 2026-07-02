<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;
use Repository\PengaduanRepository;

/**
 * PengaduanController (Admin) — Mengelola keluhan masyarakat dan feedback.
 */
final class PengaduanController extends Controller
{
    private PengaduanRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new PengaduanRepository($this->db);
    }

    // GET /admin/pengaduan
    public function index(): void
    {
        $filters = [
            'status'    => $this->clean($this->input('status', '')),
            'kategori'  => $this->clean($this->input('kategori', '')),
            'prioritas' => $this->clean($this->input('prioritas', '')),
            'search'    => $this->clean($this->input('q', '')),
        ];
        $page   = max(1, (int)$this->input('page', 1));
        $result = $this->repo->findAll($filters, $page);
        $stats  = $this->repo->countByStatus();
        $flash  = $this->getFlash();

        $this->render('admin/pengaduan/index', compact('result', 'filters', 'stats', 'flash'), 'admin');
    }

    // GET /admin/pengaduan/show/{id}
    public function show(int $id): void
    {
        $pengaduan = $this->repo->findById($id);
        if (!$pengaduan) $this->abort(404);

        $media   = $this->repo->getMedia($id);
        $hasilAi = $this->repo->getHasilAi($id);
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('admin/pengaduan/show', compact('pengaduan', 'media', 'hasilAi', 'csrfToken'), 'admin');
    }

    // POST /admin/pengaduan/tanggapan/{id}
    public function tanggapan(int $id): void
    {
        $pengaduan = $this->repo->findById($id);
        if (!$pengaduan) $this->abort(404);

        $tanggapan = $this->clean($this->input('tanggapan', ''));
        $status    = $this->clean($this->input('status', ''));

        if (!$status) {
            $this->flash('danger', 'Status penanganan wajib dipilih.');
            $this->redirect("admin/pengaduan/show/$id");
        }

        $updateData = [
            'status'          => $status,
            'tanggapan_admin' => $tanggapan,
            'admin_id'        => $this->authId(),
            'tanggal_tindak'  => date('Y-m-d H:i:s'),
        ];

        if ($status === 'selesai') {
            $updateData['selesai_at'] = date('Y-m-d H:i:s');
        }

        $this->repo->update($id, $updateData);

        // Kirim notifikasi ke warga pelapor
        $this->db->insert('notifikasi', [
            'user_id' => $pengaduan['user_id'],
            'judul'   => 'Update Pengaduan',
            'pesan'   => "Laporan Anda ({$pengaduan['nomor']}) statusnya berubah menjadi: " . strtoupper($status),
            'tipe'    => 'pengaduan',
            'url'     => 'warga/pengaduan/show/' . $id
        ]);

        $this->flash('success', 'Tanggapan pengaduan berhasil disimpan.');
        $this->redirect('admin/pengaduan');
    }
}
