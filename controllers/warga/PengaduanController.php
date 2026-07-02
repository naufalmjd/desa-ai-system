<?php

declare(strict_types=1);

namespace Controller\Warga;

use Core\Controller;
use Repository\PengaduanRepository;
use Service\AIService;

/**
 * PengaduanController (Warga) — Laporan & riwayat pengaduan masyarakat.
 */
final class PengaduanController extends Controller
{
    private PengaduanRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new PengaduanRepository($this->db);
    }

    // GET /warga/pengaduan
    public function index(): void
    {
        $userId = $this->authId();
        $page   = max(1, (int)$this->input('page', 1));
        $result = $this->repo->findByUserId($userId, $page);
        $flash  = $this->getFlash();

        $this->render('warga/pengaduan/index', compact('result', 'flash'), 'warga');
    }

    // GET /warga/pengaduan/create
    public function create(): void
    {
        $csrfToken = $this->session->generateCsrfToken();
        $this->render('warga/pengaduan/create', compact('csrfToken'), 'warga');
    }

    // POST /warga/pengaduan/store
    public function store(): void
    {
        $userId = $this->authId();

        $data = [
            'user_id'        => $userId,
            'nomor'          => $this->repo->generateNomor(),
            'judul'          => $this->clean($this->input('judul', '')),
            'kategori'       => $this->clean($this->input('kategori', '')),
            'deskripsi'      => $this->clean($this->input('deskripsi', '')),
            'lokasi_alamat'  => $this->clean($this->input('lokasi_alamat', '')),
            'latitude'       => $this->input('latitude') ?: null,
            'longitude'      => $this->input('longitude') ?: null,
            'status'         => 'menunggu',
            'prioritas'      => 'sedang',
        ];

        // Validasi
        foreach (['judul', 'kategori', 'deskripsi', 'lokasi_alamat'] as $field) {
            if (empty($data[$field])) {
                $this->flash('danger', 'Semua field wajib diisi.');
                $this->redirect('warga/pengaduan/create');
            }
        }

        $pengaduanId = $this->repo->create($data);

        // Upload media & AI detection
        $mediaId = null;
        if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $mediaId = $this->uploadMedia($pengaduanId, $_FILES['foto'], 'foto');
        }

        // Jalankan AI detection jika ada foto
        if ($mediaId) {
            $this->runAiDetection($pengaduanId, $mediaId);
        }

        // Notifikasi admin
        $this->db->insert('notifikasi', [
            'user_id' => $this->db->fetchColumn(
                'SELECT u.id FROM users u JOIN roles r ON r.id=u.role_id WHERE r.name=\'admin\' LIMIT 1'
            ),
            'judul'   => 'Pengaduan Baru',
            'pesan'   => "Pengaduan baru: {$data['judul']} — {$data['nomor']}",
            'tipe'    => 'pengaduan',
        ]);

        $this->flash('success', "Pengaduan berhasil dikirim! Nomor: {$data['nomor']}");
        $this->redirect('warga/pengaduan');
    }

    // GET /warga/pengaduan/show/{id}
    public function show(int $id): void
    {
        $pengaduan = $this->repo->findById($id);

        if (!$pengaduan || (int)$pengaduan['user_id'] !== $this->authId()) {
            $this->abort(404);
        }

        $media   = $this->repo->getMedia($id);
        $hasilAi = $this->repo->getHasilAi($id);

        $this->render('warga/pengaduan/show', compact('pengaduan', 'media', 'hasilAi'), 'warga');
    }

    // POST /warga/pengaduan/analyze — AJAX AI detect
    public function analyze(): void
    {
        if (!$this->isAjax()) $this->abort(400);

        try {
            $file = $_FILES['foto'] ?? null;
            if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
                $this->jsonError('File tidak valid.');
            }

            // Simpan sementara
            $tmp = $file['tmp_name'];
            $ai  = new AIService();
            $result = $ai->detectImage($tmp);

            $this->jsonSuccess([
                'kategori'   => $result['kategori_deteksi'],
                'confidence' => round($result['confidence_score'], 2),
                'prioritas'  => $result['prioritas_ai'],
                'labels'     => $result['labels'],
            ], 'Deteksi AI selesai.');
        } catch (\Throwable $e) {
            $this->jsonError('AI server tidak tersedia. ' . ($e->getMessage()));
        }
    }

    private function uploadMedia(int $pengaduanId, array $file, string $tipe): int
    {
        $dir = UPLOAD_PATH . '/pengaduan/' . date('Y/m');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $mime     = $file['type'];
        $allowed  = $tipe === 'foto' ? ALLOWED_IMAGE : ALLOWED_VIDEO;
        if (!in_array($mime, $allowed, true)) return 0;

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest     = $dir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return 0;

        return (int)$this->repo->addMedia([
            'pengaduan_id' => $pengaduanId,
            'tipe'         => $tipe,
            'nama_file'    => $filename,
            'original_name'=> $file['name'],
            'mime_type'    => $mime,
            'ukuran'       => $file['size'],
            'path'         => str_replace(ROOT_PATH, '', $dest),
        ]);
    }

    private function runAiDetection(int $pengaduanId, int $mediaId): void
    {
        try {
            $media = $this->db->fetchOne(
                'SELECT * FROM pengaduan_media WHERE id = ?', [$mediaId]
            );
            if (!$media) return;

            $imagePath = ROOT_PATH . $media['path'];
            $ai        = new AIService();
            $result    = $ai->detectImage($imagePath);

            $this->repo->saveHasilAi([
                'pengaduan_id'    => $pengaduanId,
                'media_id'        => $mediaId,
                'model'           => 'YOLOv8',
                'kategori_deteksi'=> $result['kategori_deteksi'],
                'confidence_score'=> $result['confidence_score'],
                'prioritas_ai'    => $result['prioritas_ai'],
                'bounding_boxes'  => json_encode($result['bounding_boxes']),
                'labels'          => json_encode($result['labels']),
                'raw_response'    => json_encode($result['raw_response']),
                'processing_time' => $result['processing_time'],
            ]);

            // Update prioritas pengaduan berdasarkan AI
            $this->db->update('pengaduan', ['prioritas' => $result['prioritas_ai']], 'id = ?', [$pengaduanId]);

        } catch (\Throwable) {
            // AI server tidak tersedia — lanjut tanpa error fatal
        }
    }
}
