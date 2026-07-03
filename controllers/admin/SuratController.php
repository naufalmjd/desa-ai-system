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

    // GET /admin/surat/templates
    public function templates(): void
    {
        $templates = $this->db->fetchAll('SELECT * FROM jenis_surat WHERE is_active = 1 ORDER BY id DESC');
        $flash = $this->getFlash();
        $csrfToken = $this->session->generateCsrfToken();
        $this->render('admin/surat/templates', compact('templates', 'flash', 'csrfToken'), 'admin');
    }

    // GET /admin/surat/templatecreate
    public function templatecreate(): void
    {
        $csrfToken = $this->session->generateCsrfToken();
        $this->render('admin/surat/template_create', compact('csrfToken'), 'admin');
    }

    // POST /admin/surat/templatestore
    public function templatestore(): void
    {
        $kode = strtoupper($this->clean($this->input('kode', '')));
        $nama = $this->clean($this->input('nama', ''));
        $deskripsi = $this->clean($this->input('deskripsi', ''));
        $estimasi = (int)$this->input('estimasi_hari', 3);
        $persyaratanRaw = $this->input('persyaratan', []);

        if (!$kode || !$nama || empty($_FILES['template_file']['name'])) {
            $this->flash('danger', 'Kode, nama, dan file template wajib diisi.');
            $this->redirect('admin/surat/templatecreate');
            return;
        }

        // Clean requirements array
        $persyaratan = [];
        if (is_array($persyaratanRaw)) {
            foreach ($persyaratanRaw as $p) {
                $pCleaned = $this->clean($p);
                if ($pCleaned !== '') {
                    $persyaratan[] = $pCleaned;
                }
            }
        }
        $persyaratanJson = json_encode($persyaratan, JSON_UNESCAPED_UNICODE);

        // Process template file upload
        $templatePath = null;
        if ($_FILES['template_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['template_file'];
            $allowedExtensions = ['doc', 'docx', 'pdf'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions, true)) {
                $this->flash('danger', 'Format file template tidak valid. Hanya menerima file Word (.doc, .docx) dan PDF.');
                $this->redirect('admin/surat/templatecreate');
                return;
            }

            $dir = UPLOAD_PATH . '/templates';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $filename = strtolower($kode) . '_template_' . time() . '.' . $ext;
            $dest = $dir . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $templatePath = '/public/uploads/templates/' . $filename;
            } else {
                $this->flash('danger', 'Gagal mengunggah file template.');
                $this->redirect('admin/surat/templatecreate');
                return;
            }
        }

        try {
            // Check if code already exists
            $exists = $this->db->fetchOne('SELECT id FROM jenis_surat WHERE kode = ?', [$kode]);
            if ($exists) {
                $this->flash('danger', "Template dengan kode '$kode' sudah ada.");
                $this->redirect('admin/surat/templatecreate');
                return;
            }

            $this->db->insert('jenis_surat', [
                'kode' => $kode,
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'persyaratan' => $persyaratanJson,
                'template_path' => $templatePath,
                'estimasi_hari' => $estimasi,
                'is_active' => 1
            ]);

            $this->flash('success', 'Template baru berhasil ditambahkan.');
            $this->redirect('admin/surat/templates');
        } catch (\Exception $e) {
            $this->flash('danger', 'Gagal menyimpan template: ' . $e->getMessage());
            $this->redirect('admin/surat/templatecreate');
        }
    }

    // POST /admin/surat/templateupload/{id}
    public function templateupload(int $id): void
    {
        if (empty($_FILES['template_file']['name'])) {
            $this->flash('danger', 'Silakan pilih file template terlebih dahulu.');
            $this->redirect('admin/surat/templates');
            return;
        }

        $template = $this->db->fetchOne('SELECT * FROM jenis_surat WHERE id = ?', [$id]);
        if (!$template) {
            $this->flash('danger', 'Template surat tidak ditemukan.');
            $this->redirect('admin/surat/templates');
            return;
        }

        $file = $_FILES['template_file'];
        $allowedExtensions = ['doc', 'docx', 'pdf'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions, true)) {
            $this->flash('danger', 'Format file template tidak valid. Hanya menerima file Word (.doc, .docx) dan PDF.');
            $this->redirect('admin/surat/templates');
            return;
        }

        $dir = UPLOAD_PATH . '/templates';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = strtolower($template['kode']) . '_template_' . time() . '.' . $ext;
        $dest = $dir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $templatePath = '/public/uploads/templates/' . $filename;
            
            // Delete old file if exists
            if (!empty($template['template_path'])) {
                $oldFile = ROOT_PATH . $template['template_path'];
                if (is_file($oldFile)) @unlink($oldFile);
            }

            $this->db->update('jenis_surat', ['template_path' => $templatePath], 'id = ?', [$id]);
            $this->flash('success', "File template untuk '{$template['nama']}' berhasil diunggah.");
        } else {
            $this->flash('danger', 'Gagal mengunggah file template.');
        }

        $this->redirect('admin/surat/templates');
    }

    // POST /admin/surat/templatedelete/{id}
    public function templatedelete(int $id): void
    {
        try {
            // Try to physically delete
            $this->db->delete('jenis_surat', 'id = ?', [$id]);
            if ($this->isAjax()) {
                $this->jsonSuccess(null, 'Template berhasil dihapus.');
                return;
            }
            $this->flash('success', 'Template berhasil dihapus.');
        } catch (\Exception $e) {
            // If foreign key constraint violates, soft delete/deactivate
            $this->db->update('jenis_surat', ['is_active' => 0], 'id = ?', [$id]);
            if ($this->isAjax()) {
                $this->jsonSuccess(null, 'Template dinonaktifkan karena telah digunakan dalam pengajuan.');
                return;
            }
            $this->flash('success', 'Template dinonaktifkan karena telah digunakan dalam pengajuan.');
        }
        $this->redirect('admin/surat/templates');
    }
}
