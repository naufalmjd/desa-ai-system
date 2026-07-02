<?php $pageTitle = 'Persetujuan Surat'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card" data-aos="fade-up">
    <div class="card-header bg-white py-3">
        <i class="bi bi-file-earmark-check-fill me-2 text-warning"></i>Daftar Pengajuan Surat Perlu Persetujuan Anda
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
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $s): ?>
                    <tr>
                        <td><code class="text-primary"><?= htmlspecialchars($s['nomor']) ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($s['jenis_nama'] ?? $s['jenis_kode']) ?></td>
                        <td><?= htmlspecialchars($s['pemohon_nama']) ?></td>
                        <td><code><?= htmlspecialchars($s['nik']) ?></code></td>
                        <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                        <td>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle badge-status">
                                MENUNGGU TTD
                            </span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/kepaladesa/surat/show/<?= $s['id'] ?>" class="btn btn-sm btn-warning text-white">
                                <i class="bi bi-pencil-square me-1"></i> Tinjau & TTD
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($result['data'])): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-file-earmark-check fs-2 d-block mb-2 text-success"></i>
                            Bagus! Tidak ada antrean pengajuan surat saat ini.
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
                    <a class="page-link" href="?page=<?= $i ?>">
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
