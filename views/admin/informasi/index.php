<?php $pageTitle = 'Kelola Informasi & Berita'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Daftar Berita/Pengumuman -->
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-newspaper me-2 text-primary"></i>Kelola Kabar/Berita Desa</span>
                <button class="btn btn-xs btn-primary py-1 px-2" style="font-size: .75rem" disabled><i class="bi bi-plus-lg"></i> Berita Baru</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th><th>Kategori</th><th>Status</th><th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($berita as $b): ?>
                            <tr>
                                <td class="fw-semibold small text-truncate" style="max-width: 220px"><?= htmlspecialchars($b['judul']) ?></td>
                                <td><span class="badge bg-light border text-dark"><?= htmlspecialchars($b['kategori']) ?></span></td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle badge-status">
                                        <?= strtoupper($b['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($b['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($berita)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4 small">Belum ada berita dibuat</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bantuan Sosial -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-heart-fill me-2 text-danger"></i>Program Bantuan Sosial (Bansos)</span>
                <button class="btn btn-xs btn-outline-danger py-1 px-2" style="font-size: .75rem" disabled><i class="bi bi-plus-lg"></i> Bansos Baru</button>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($bantuan as $bs): ?>
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: .8rem"><?= htmlspecialchars($bs['nama']) ?></h6>
                            <span class="badge bg-success-subtle text-success border border-success-subtle badge-status"><?= strtoupper($bs['status']) ?></span>
                        </div>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($bs['deskripsi']) ?></p>
                        <div class="small mb-1">
                            <span class="text-muted">Sasaran:</span> <strong class="text-dark"><?= htmlspecialchars($bs['sasaran']) ?></strong>
                        </div>
                        <div class="small">
                            <span class="text-muted">Besaran:</span> <strong class="text-primary"><?= htmlspecialchars($bs['besaran']) ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($bantuan)): ?>
                    <div class="text-center py-4 text-muted small">Belum ada program bansos terdaftar</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
