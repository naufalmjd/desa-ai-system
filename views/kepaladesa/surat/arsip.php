<?php $pageTitle = 'Arsip Persetujuan Surat'; ?>
<?php ob_start(); ?>

<div class="card" data-aos="fade-up">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-archive-fill me-2 text-warning"></i>Riwayat Persetujuan Surat TTD Elektronik</span>
        <div class="d-flex gap-2">
            <a href="?status=selesai" class="btn btn-xs btn-success py-1 px-2" style="font-size: .72rem">Selesai</a>
            <a href="?status=ditolak" class="btn btn-xs btn-danger py-1 px-2" style="font-size: .72rem">Ditolak</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Surat</th>
                        <th>Jenis Surat</th>
                        <th>Pemohon</th>
                        <th>NIK</th>
                        <th>Tanggal Diproses</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $s): ?>
                    <tr>
                        <td><code class="text-primary"><?= htmlspecialchars($s['nomor']) ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($s['jenis_nama'] ?? $s['jenis_kode']) ?></td>
                        <td><?= htmlspecialchars($s['pemohon_nama']) ?></td>
                        <td><code><?= htmlspecialchars($s['nik']) ?></code></td>
                        <td><?= date('d/m/Y H:i', strtotime($s['updated_at'])) ?></td>
                        <td>
                            <?php $sc = $s['status'] === 'selesai' ? 'success' : 'danger'; ?>
                            <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?> border border-<?= $sc ?>-subtle badge-status">
                                <?= strtoupper($s['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($result['data'])): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-folder2-open fs-2 d-block mb-2"></i>
                            Belum ada arsip surat.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($result['last_page'] > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">
            Menampilkan <?= count($result['data']) ?> dari <?= number_format($result['total']) ?> data
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $result['last_page']; $i++): ?>
                <li class="page-item <?= $i===$result['current_page']?'active':'' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&status=<?= urlencode($s['status'] ?? 'selesai') ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/kepaladesa.php'; ?>
