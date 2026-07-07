<?php
ob_start();
$pageTitle = "Kelola Beranda";
?>

<div class="container-fluid px-0" data-aos="fade-up">
    <!-- Header Page -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-gear-fill me-2 text-primary"></i>Kelola Beranda</h4>
            <p class="text-muted small mb-0">Ubah seluruh tampilan teks, data statistik, dan nomor WhatsApp ambulans pada halaman depan.</p>
        </div>
    </div>

    <!-- Alert Flash Message -->
    <?php if ($flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/admin/settings/save" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

        <div class="row g-4">
            <!-- Kolom Kiri: Pengumuman & Ambulans -->
            <div class="col-lg-8">
                <!-- Card 1: Pengumuman Kades -->
                <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                    <div class="card-header bg-white py-3 border-0 px-4">
                        <span class="fw-bold text-dark fs-6">
                            <i class="bi bi- megaphone-fill me-2 text-warning"></i>Pengumuman Penting Kepala Desa
                        </span>
                    </div>
                    <div class="card-body pt-0 px-4 pb-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Judul Pengumuman</label>
                            <input type="text" name="announcement_title" class="form-control rounded-3" 
                                   value="<?= htmlspecialchars($settings['announcement_title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Tanggal Terbit</label>
                            <input type="text" name="announcement_date" class="form-control rounded-3" 
                                   value="<?= htmlspecialchars($settings['announcement_date']) ?>" placeholder="Contoh: 04 Juli 2026" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-muted">Isi Pengumuman</label>
                            <textarea name="announcement_content" class="form-control rounded-3" rows="4" required><?= htmlspecialchars($settings['announcement_content']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Layanan Ambulans Darurat -->
                <div class="card border-0 rounded-4 shadow-sm bg-white">
                    <div class="card-header bg-white py-3 border-0 px-4">
                        <span class="fw-bold text-dark fs-6">
                            <i class="bi bi-heart-pulse-fill me-2 text-danger"></i>Layanan Ambulans Darurat
                        </span>
                    </div>
                    <div class="card-body pt-0 px-4 pb-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Nomor WhatsApp Admin Siaga (Tujuan SMS/Chat WA)</label>
                            <div class="input-group">
                                <span class="input-group-text rounded-start-3"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="ambulance_phone" class="form-control rounded-end-3" 
                                       value="<?= htmlspecialchars($settings['ambulance_phone']) ?>" placeholder="Contoh: 628123456789 atau 08123456789" required>
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.72rem;">Pastikan menggunakan nomor aktif WA dengan kode negara (misal: 628...) atau diawali angka 0.</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-muted">Deskripsi Layanan Panggilan</label>
                            <textarea name="ambulance_description" class="form-control rounded-3" rows="3" required><?= htmlspecialchars($settings['ambulance_description']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Statistik & Aksi Simpan -->
            <div class="col-lg-4">
                <!-- Card 3: Statistik Demografi -->
                <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                    <div class="card-header bg-white py-3 border-0 px-4">
                        <span class="fw-bold text-dark fs-6">
                            <i class="bi bi-graph-up-arrow me-2 text-accent"></i>Statistik Demografi Desa
                        </span>
                    </div>
                    <div class="card-body pt-0 px-4 pb-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Total Penduduk</label>
                            <input type="text" name="stat_penduduk" class="form-control rounded-3" 
                                   value="<?= htmlspecialchars($settings['stat_penduduk']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Luas Wilayah</label>
                            <input type="text" name="stat_luas" class="form-control rounded-3" 
                                   value="<?= htmlspecialchars($settings['stat_luas']) ?>" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-muted">Wilayah Administrasi (RT/RW)</label>
                            <input type="text" name="stat_wilayah" class="form-control rounded-3" 
                                   value="<?= htmlspecialchars($settings['stat_wilayah']) ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Action Card -->
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold">
                        <i class="bi bi-check-circle me-2"></i>Simpan Pengaturan
                    </button>
                    <a href="<?= APP_URL ?>/admin/dashboard" class="btn btn-light w-100 py-2.5 rounded-3 fw-semibold mt-2">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/admin.php';
?>