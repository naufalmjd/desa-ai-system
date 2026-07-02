<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;
use Service\SuratService;

/**
 * SuratController (Admin) — Kelola pengajuan surat.
 */
final class SuratController extends Controller
{
    private SuratService $suratService;

    public function __construct()
    {
        parent::__construct();
        $this->suratService = new SuratService($this->db);
    }

    // GET /admin/surat
    public function index(): void
    {
        $filters = [
            'status'    => $this->clean($this->input('status', '')),
            'jenis_id'  => (int)$this->input('jenis_id', 0),
            'search'    => $this->clean($this->input('q', '')),
            'tanggal_dari'   => $this->input('dari', ''),
            'tanggal_sampai' => $this->input('sampai', ''),
        ];
        $page    = max(1, (int)$this->input('page', 1));
        $result  = $this->suratService->getAll($filters, $page);
        $stats   = $this->suratService->getDashboardStats();
        $jenisList = $this->suratService->getJenisSurat();
        $flash   = $this->getFlash();

        $this->render('admin/surat/index', compact('result', 'filters', 'stats', 'jenisList', 'flash'), 'admin');
    }

    // GET /admin/surat/show/{id}
    public function show(int $id): void
    {
        $surat    = $this->suratService->getById($id);
        if (!$surat) $this->abort(404);
        $lampiran = $this->db->fetchAll('SELECT * FROM lampiran_surat WHERE pengajuan_id=?', [$id]);

        $csrfToken = $this->session->generateCsrfToken();
        $this->render('admin/surat/show', compact('surat', 'lampiran', 'csrfToken'), 'admin');
    }

    // POST /admin/surat/verifikasi/{id}
    public function verifikasi(int $id): void
    {
        $catatan = $this->clean($this->input('catatan', ''));
        $result  = $this->suratService->verifikasi($id, $this->authId(), $catatan);

        if ($this->isAjax()) {
            $result['success']
                ? $this->jsonSuccess(null, $result['message'])
                : $this->jsonError($result['message']);
        }

        $this->flash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('admin/surat');
    }

    // POST /admin/surat/kirimkades/{id}
    public function kirimkades(int $id): void
    {
        $result = $this->suratService->kirimKeKades($id, $this->authId());
        if ($this->isAjax()) $result['success'] ? $this->jsonSuccess() : $this->jsonError($result['message']);

        $this->flash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('admin/surat');
    }

    // POST /admin/surat/tolak/{id}
    public function tolak(int $id): void
    {
        $alasan = $this->clean($this->input('alasan', ''));
        if (!$alasan) {
            $this->flash('danger', 'Alasan penolakan wajib diisi.');
            $this->redirect("admin/surat/show/$id");
        }

        $result = $this->suratService->tolakAdmin($id, $this->authId(), $alasan);
        $this->flash($result['success'] ? 'success' : 'danger', $result['message']);
        $this->redirect('admin/surat');
    }
}
