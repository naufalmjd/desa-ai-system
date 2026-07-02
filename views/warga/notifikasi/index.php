<?php $pageTitle = 'Pemberitahuan & Notifikasi'; ?>
<?php ob_start(); ?>

<div class="card" data-aos="fade-up">
    <div class="card-header bg-white py-3">
        <i class="bi bi-bell-fill me-2 text-danger"></i>Kotak Masuk Notifikasi Anda
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <?php foreach ($notifikasi as $n): ?>
            <div class="list-group-item py-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 text-primary flex-shrink-0">
                        <i class="bi <?= $n['tipe'] === 'surat' ? 'bi-file-earmark-text' : ($n['tipe'] === 'pengaduan' ? 'bi-megaphone' : 'bi-info-circle') ?> fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: .85rem"><?= htmlspecialchars($n['judul']) ?></h6>
                            <small class="text-muted" style="font-size: .7rem"><?= date('d M Y H:i', strtotime($n['created_at'])) ?></small>
                        </div>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($n['pesan']) ?></p>
                        <?php if ($n['url']): ?>
                        <a href="<?= APP_URL ?>/<?= htmlspecialchars($n['url']) ?>" class="btn btn-xs btn-outline-primary py-1 px-2" style="font-size: .7rem">
                            Lihat Rincian <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($notifikasi)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-2 d-block mb-1"></i>
                Tidak ada notifikasi baru untuk Anda.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
