<?php $pageTitle = 'Informasi Desa'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Kolom Berita & Pengumuman -->
    <div class="col-lg-8">
        <h5 class="fw-bold mb-3"><i class="bi bi-newspaper me-2 text-primary"></i>Kabar Desa Sukamaju</h5>
        <div class="row g-3">
            <?php foreach ($berita as $b): ?>
            <div class="col-md-6">
                <div class="card h-100 border">
                    <div class="rounded-top-3 bg-secondary bg-gradient" style="height: 120px; background: linear-gradient(135deg, #1e4080, #059669) !important">
                        <div class="d-flex align-items-center justify-content-center h-100 text-white opacity-25">
                            <i class="bi bi-image fs-1"></i>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: .65rem">
                                <?= strtoupper($b['kategori']) ?>
                            </span>
                            <small class="text-muted" style="font-size: .7rem"><?= date('d M Y', strtotime($b['created_at'])) ?></small>
                        </div>
                        <h6 class="fw-bold text-dark mb-2" style="font-size: .85rem"><?= htmlspecialchars($b['judul']) ?></h6>
                        <p class="text-muted small mb-0"><?= htmlspecialchars($b['excerpt'] ?? substr(strip_tags($b['konten']), 0, 100) . '...') ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($berita)): ?>
            <div class="col-12 text-center py-5 text-muted small">
                <i class="bi bi-newspaper fs-2 d-block mb-1"></i>Belum ada informasi dipublikasikan
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kolom Bantuan Sosial -->
    <div class="col-lg-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-heart-fill me-2 text-danger"></i>Program Bantuan Sosial</h5>
        <div class="list-group">
            <?php foreach ($bantuan as $bs): ?>
            <div class="list-group-item p-3 border mb-2 rounded-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: .8rem"><?= htmlspecialchars($bs['nama']) ?></h6>
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
            <div class="text-center py-5 text-muted small">
                <i class="bi bi-heartbreak fs-2 d-block mb-1"></i>Belum ada program bantuan aktif
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
