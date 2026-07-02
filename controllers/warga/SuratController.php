<?php

declare(strict_types=1);

namespace Controller\Warga;

use Core\Controller;
use Service\SuratService;

/**
 * SuratController (Warga) — Pengajuan dan Tracking Surat.
 */
final class SuratController extends Controller
{
    private SuratService $suratService;

    public function __construct()
    {
        parent::__construct();
        $this->suratService = new SuratService($this->db);
    }

    // GET /warga/surat
    public function index(): void
    {
        $userId  = $this->authId();
        $filters = [
            'status' => $this->clean($this->input('status', '')),
        ];
        $page    = max(1, (int)$this->input('page', 1));
        $result  = $this->suratService->getByUser($userId, $filters, $page);
        $flash   = $this->getFlash();

        $this->render('warga/surat/index', compact('result', 'filters', 'flash'), 'warga');
    }

    // GET /warga/surat/create
    public function create(): void
    {
        $jenisSurat = $this->suratService->getJenisSurat();
        $csrfToken  = $this->session->generateCsrfToken();

        $this->render('warga/surat/create', compact('jenisSurat', 'csrfToken'), 'warga');
    }

    // POST /warga/surat/store
    public function store(): void
    {
        $data = [
            'jenis_surat_id' => (int)$this->input('jenis_surat_id'),
            'keperluan'      => $this->clean($this->input('keperluan', '')),
            'catatan'        => $this->clean($this->input('catatan', '')),
        ];

        if (!$data['jenis_surat_id'] || !$data['keperluan']) {
            $this->flash('danger', 'Jenis surat dan keperluan wajib diisi.');
            $this->redirect('warga/surat/create');
        }

        $result = $this->suratService->ajukan($this->authId(), $data, $_FILES);

        if ($result['success']) {
            $this->flash('success', "Pengajuan berhasil! Nomor: {$result['nomor']}");
            $this->redirect('warga/surat');
        }

        $this->flash('danger', $result['message']);
        $this->redirect('warga/surat/create');
    }

    // GET /warga/surat/show/{id}
    public function show(int $id): void
    {
        $surat = $this->suratService->getById($id);

        if (!$surat || $surat['user_id'] !== $this->authId()) {
            $this->abort(404, 'Surat tidak ditemukan.');
        }

        $lampiran  = $this->db->fetchAll(
            'SELECT * FROM lampiran_surat WHERE pengajuan_id = ?', [$id]
        );

        $this->render('warga/surat/show', compact('surat', 'lampiran'), 'warga');
    }

    // GET /warga/surat/tracking
    public function tracking(): void
    {
        $userId   = $this->authId();
        $suratList= $this->db->fetchAll(
            'SELECT ps.*, js.nama AS jenis_nama FROM pengajuan_surat ps
             JOIN jenis_surat js ON js.id = ps.jenis_surat_id
             WHERE ps.user_id=? AND ps.deleted_at IS NULL ORDER BY ps.created_at DESC',
            [$userId]
        );

        $selected = null;
        if ($id = (int)$this->input('id')) {
            $selected = $this->suratService->getById($id);
            if ($selected && $selected['user_id'] !== $userId) $selected = null;
        }

        $this->render('warga/surat/tracking', compact('suratList', 'selected'), 'warga');
    }
}
