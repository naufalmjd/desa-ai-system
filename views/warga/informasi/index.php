<?php $pageTitle = 'Informasi Desa'; ?>
<?php ob_start(); ?>

<style>
.card-hover-effect:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    border-color: #1e4080 !important;
}
</style>

<div class="row g-4" data-aos="fade-up">
    <!-- Kolom Berita & Pengumuman -->
    <div class="col-lg-8">
        <h5 class="fw-bold mb-3"><i class="bi bi-newspaper me-2 text-primary"></i>Kabar Desa Sukamaju</h5>
        <div class="row g-3">
            <?php foreach ($berita as $b): ?>
            <div class="col-md-6">
                <?php if (!empty($b['file_path'])): ?>
                    <a href="<?= APP_URL ?>/public/uploads/berita/<?= htmlspecialchars($b['file_path']) ?>" target="_blank" class="text-decoration-none">
                <?php else: ?>
                    <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#viewKontenModal"
                       data-judul="<?= htmlspecialchars($b['judul']) ?>"
                       data-kategori="<?= htmlspecialchars($b['kategori']) ?>"
                       data-tanggal="<?= date('d M Y', strtotime($b['created_at'])) ?>"
                       data-konten="<?= htmlspecialchars($b['konten']) ?>">
                <?php endif; ?>
                <div class="card h-100 border card-hover-effect" style="transition: transform .25s, box-shadow .25s;">
                    <?php if (!empty($b['thumbnail'])): ?>
                        <img src="<?= APP_URL ?>/public/uploads/berita/<?= htmlspecialchars($b['thumbnail']) ?>" class="rounded-top-3 w-100" style="height: 120px; object-fit: cover;" alt="Thumbnail">
                    <?php else: ?>
                        <div class="rounded-top-3 bg-secondary bg-gradient" style="height: 120px; background: linear-gradient(135deg, #1e4080, #059669) !important">
                            <div class="d-flex align-items-center justify-content-center h-100 text-white opacity-25">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: .65rem">
                                <?= strtoupper($b['kategori']) ?>
                            </span>
                            <small class="text-muted" style="font-size: .7rem"><?= date('d M Y', strtotime($b['created_at'])) ?></small>
                        </div>
                        <h6 class="fw-bold text-dark mb-2" style="font-size: .85rem">
                            <?php if (!empty($b['file_path'])): ?>
                                <i class="bi bi-file-earmark-arrow-down-fill me-1 text-primary"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($b['judul']) ?>
                        </h6>
                        <p class="text-muted small mb-0"><?= htmlspecialchars($b['excerpt'] ?? substr(strip_tags($b['konten']), 0, 100) . '...') ?></p>
                    </div>
                </div>
                </a>
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

<!-- Modal View Konten Berita -->
<div class="modal fade" id="viewKontenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header py-3 px-4 border-0">
                <h5 class="modal-title fw-bold text-dark" id="modalJudulBerita"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="modalKategoriBerita" style="font-size: .7rem"></span>
                    <small class="text-muted" id="modalTanggalBerita"></small>
                </div>
                <hr class="my-2 opacity-25">
                <div id="modalIsiBerita" class="text-dark mt-3" style="line-height: 1.7; font-size: 0.95rem;"></div>
            </div>
            <div class="modal-footer border-0 px-4 py-3">
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewKontenModal = document.getElementById('viewKontenModal');
    if (viewKontenModal) {
        viewKontenModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const judul = button.getAttribute('data-judul');
            const kategori = button.getAttribute('data-kategori');
            const tanggal = button.getAttribute('data-tanggal');
            const konten = button.getAttribute('data-konten');
            
            document.getElementById('modalJudulBerita').textContent = judul;
            document.getElementById('modalKategoriBerita').textContent = kategori.toUpperCase();
            document.getElementById('modalTanggalBerita').textContent = tanggal;
            document.getElementById('modalIsiBerita').innerHTML = konten.replace(/\n/g, '<br>');
        });
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
