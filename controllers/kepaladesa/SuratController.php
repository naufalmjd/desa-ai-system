<?php

declare(strict_types=1);

namespace Controller\Kepaladesa;

use Core\Controller;
use Service\SuratService;

/**
 * SuratController (Kepala Desa) — Persetujuan & penolakan surat.
 */
final class SuratController extends Controller
{
    private SuratService $suratService;

    public function __construct()
    {
        parent::__construct();
        $this->suratService = new SuratService($this->db);
    }

    // GET /kepaladesa/surat
    public function index(): void
    {
        $filters = ['status' => 'menunggu_persetujuan'];
        $result  = $this->suratService->getAll($filters, max(1, (int)$this->input('page', 1)));
        $flash   = $this->getFlash();

        $this->render('kepaladesa/surat/index', compact('result', 'flash'), 'kepaladesa');
    }

    // GET /kepaladesa/surat/show/{id}
    public function show(int $id): void
    {
        $surat     = $this->suratService->getById($id);
        if (!$surat) $this->abort(404);
        $lampiran  = $this->db->fetchAll('SELECT * FROM lampiran_surat WHERE pengajuan_id=?', [$id]);
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('kepaladesa/surat/show', compact('surat', 'lampiran', 'csrfToken'), 'kepaladesa');
    }

    // POST /kepaladesa/surat/setujui/{id}
    public function setujui(int $id): void
    {
        $catatan = $this->clean($this->input('catatan', ''));
        $result  = $this->suratService->setujui($id, $this->authId(), $catatan);

        if ($this->isAjax()) $result['success'] ? $this->jsonSuccess() : $this->jsonError($result['message']);

        $this->flash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('kepaladesa/surat');
    }

    // POST /kepaladesa/surat/tolak/{id}
    public function tolak(int $id): void
    {
        $alasan = $this->clean($this->input('alasan', ''));
        if (!$alasan) {
            $this->flash('danger', 'Alasan penolakan wajib diisi.');
            $this->redirect("kepaladesa/surat/show/$id");
        }

        $result = $this->suratService->tolakKades($id, $this->authId(), $alasan);
        $this->flash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('kepaladesa/surat');
    }

    // GET /kepaladesa/surat/arsip
    public function arsip(): void
    {
        $filters = ['status' => $this->clean($this->input('status', 'selesai'))];
        $result  = $this->suratService->getAll($filters, max(1, (int)$this->input('page', 1)));

        $this->render('kepaladesa/surat/arsip', compact('result'), 'kepaladesa');
    }
}
