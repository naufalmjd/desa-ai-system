<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;
use Repository\PendudukRepository;

/**
 * PendudukController (Admin) — CRUD Data Penduduk.
 */
final class PendudukController extends Controller
{
    private PendudukRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new PendudukRepository($this->db);
    }

    // GET /admin/penduduk
    public function index(): void
    {
        $filters = [
            'search' => $this->clean($this->input('q', '')),
            'jk'     => $this->clean($this->input('jk', '')),
            'status' => $this->clean($this->input('status', '')),
        ];
        $page   = max(1, (int)$this->input('page', 1));
        $result = $this->repo->findAll($filters, $page);
        $stats  = [
            'total'  => $this->repo->countTotal(),
            'byAgama'=> $this->repo->countByAgama(),
        ];
        $flash  = $this->getFlash();

        $this->render('admin/penduduk/index', compact('result', 'filters', 'stats', 'flash'), 'admin');
    }

    // GET /admin/penduduk/create
    public function create(): void
    {
        $csrfToken = $this->session->generateCsrfToken();
        $this->render('admin/penduduk/create', compact('csrfToken'), 'admin');
    }

    // POST /admin/penduduk/store
    public function store(): void
    {
        $data = $this->collectData();

        if ($this->repo->findByNik($data['nik'])) {
            $this->flash('danger', 'NIK sudah terdaftar.');
            $this->redirect('admin/penduduk/create');
        }

        $id = $this->repo->create($data);
        $this->log('Menambah penduduk NIK: ' . $data['nik']);
        $this->flash('success', 'Data penduduk berhasil ditambahkan.');
        $this->redirect('admin/penduduk');
    }

    // GET /admin/penduduk/edit/{id}
    public function edit(int $id): void
    {
        $penduduk  = $this->repo->findById($id);
        if (!$penduduk) $this->abort(404);
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('admin/penduduk/edit', compact('penduduk', 'csrfToken'), 'admin');
    }

    // POST /admin/penduduk/update/{id}
    public function update(int $id): void
    {
        $penduduk = $this->repo->findById($id);
        if (!$penduduk) $this->abort(404);

        $data = $this->collectData();
        $this->repo->update($id, $data);
        $this->log('Mengubah penduduk ID: ' . $id);
        $this->flash('success', 'Data penduduk berhasil diperbarui.');
        $this->redirect('admin/penduduk');
    }

    // POST /admin/penduduk/delete/{id}
    public function delete(int $id): void
    {
        $penduduk = $this->repo->findById($id);
        if (!$penduduk) $this->abort(404);

        $this->repo->softDelete($id);
        $this->log('Menghapus penduduk ID: ' . $id);

        if ($this->isAjax()) $this->jsonSuccess(null, 'Data berhasil dihapus.');
        $this->flash('success', 'Data penduduk berhasil dihapus.');
        $this->redirect('admin/penduduk');
    }

    // GET /admin/penduduk/show/{id}
    public function show(int $id): void
    {
        $penduduk = $this->repo->findById($id);
        if (!$penduduk) $this->abort(404);

        $this->render('admin/penduduk/show', compact('penduduk'), 'admin');
    }

    private function collectData(): array
    {
        return [
            'nik'            => $this->clean($this->input('nik', '')),
            'no_kk'          => $this->clean($this->input('no_kk', '')),
            'nama'           => $this->clean($this->input('nama', '')),
            'tempat_lahir'   => $this->clean($this->input('tempat_lahir', '')),
            'tanggal_lahir'  => $this->input('tanggal_lahir', ''),
            'jenis_kelamin'  => $this->input('jenis_kelamin', 'L'),
            'agama'          => $this->input('agama', 'Islam'),
            'status_kawin'   => $this->input('status_kawin', 'Belum Kawin'),
            'pekerjaan'      => $this->clean($this->input('pekerjaan', '-')),
            'pendidikan'     => $this->input('pendidikan', 'Tidak/Belum Sekolah'),
            'alamat'         => $this->clean($this->input('alamat', '')),
            'rt'             => $this->clean($this->input('rt', '')),
            'rw'             => $this->clean($this->input('rw', '')),
            'dusun'          => $this->clean($this->input('dusun', '-')),
            'desa'           => DESA_NAMA,
            'kecamatan'      => $this->clean($this->input('kecamatan', DESA_KEC)),
            'kabupaten'      => $this->clean($this->input('kabupaten', DESA_KAB)),
            'provinsi'       => $this->clean($this->input('provinsi', DESA_PROV)),
            'no_hp'          => $this->clean($this->input('no_hp', '')),
            'status_penduduk'=> $this->input('status_penduduk', 'Tetap'),
        ];
    }

    private function log(string $desc): void
    {
        $this->db->insert('log_aktivitas', [
            'user_id'     => $this->authId(),
            'action'      => 'kelola_penduduk',
            'module'      => 'penduduk',
            'description' => $desc,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
