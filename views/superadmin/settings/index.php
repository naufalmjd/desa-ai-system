<?php $pageTitle = 'Konfigurasi Website'; ?>

<?php ob_start(); ?>

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex gap-2 rounded-3 mb-4 border-0 text-white" style="background: rgba(var(--bs-<?= $flash['type'] ?>-rgb), 0.2); backdrop-filter: blur(10px);" role="alert">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center" data-aos="fade-up">
    <div class="col-lg-10">
        <div class="card border-0">
            <div class="card-header bg-transparent py-3 border-0">
                <span class="fs-6 fw-bold text-white"><i class="bi bi-sliders2 me-2 text-primary"></i>Pengaturan Sistem SIAP-Desa</span>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/superadmin/settings/update" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <!-- Section 1: Informasi Aplikasi -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-white mb-3" style="font-size: 0.95rem; border-left: 3px solid var(--primary); padding-left: 0.5rem;">Informasi Utama Aplikasi</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="app_name" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Singkatan Nama Aplikasi</label>
                                <input type="text" id="app_name" name="app_name" class="form-control" value="<?= htmlspecialchars($settings['app_name']) ?>" required autocomplete="off">
                            </div>
                            <div class="col-md-8">
                                <label for="app_full" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Nama Lengkap Aplikasi</label>
                                <input type="text" id="app_full" name="app_full" class="form-control" value="<?= htmlspecialchars($settings['app_full']) ?>" required autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Informasi Wilayah -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-white mb-3" style="font-size: 0.95rem; border-left: 3px solid var(--accent); padding-left: 0.5rem;">Identitas & Geografi Desa</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="desa_nama" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Nama Desa</label>
                                <input type="text" id="desa_nama" name="desa_nama" class="form-control" value="<?= htmlspecialchars($settings['desa_nama']) ?>" required autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label for="desa_kec" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Kecamatan</label>
                                <input type="text" id="desa_kec" name="desa_kec" class="form-control" value="<?= htmlspecialchars($settings['desa_kec']) ?>" required autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label for="desa_kab" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Kabupaten / Kota</label>
                                <input type="text" id="desa_kab" name="desa_kab" class="form-control" value="<?= htmlspecialchars($settings['desa_kab']) ?>" required autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label for="desa_prov" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Provinsi</label>
                                <input type="text" id="desa_prov" name="desa_prov" class="form-control" value="<?= htmlspecialchars($settings['desa_prov']) ?>" required autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Integrasi AI Server -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-white mb-3" style="font-size: 0.95rem; border-left: 3px solid #fbbf24; padding-left: 0.5rem;">Integrasi AI & Komputer Visi</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="ai_server_url" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">URL Python AI Server</label>
                                <input type="url" id="ai_server_url" name="ai_server_url" class="form-control" value="<?= htmlspecialchars($settings['ai_server_url']) ?>" required autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <label for="ai_timeout" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Timeout Koneksi AI (Detik)</label>
                                <input type="number" id="ai_timeout" name="ai_timeout" class="form-control" value="<?= (int)$settings['ai_timeout'] ?>" min="5" max="180" required autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <hr class="border-secondary mb-4 opacity-10">

                    <!-- Actions -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 py-2.5">
                            <i class="bi bi-save-fill me-2"></i> Simpan Seluruh Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require VIEW_PATH . '/layouts/superadmin.php'; ?>
