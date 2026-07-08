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
                <form action="<?= APP_URL ?>/superadmin/settings/update" method="POST" enctype="multipart/form-data">
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

                    <!-- Section 4: Gambar Banner & Landing Page -->
                    <!-- Section 4: Konten Landing Page (Gambar & Tulisan) -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-white mb-3" style="font-size: 0.95rem; border-left: 3px solid #f43f5e; padding-left: 0.5rem;">Konten Landing Page (Gambar & Deskripsi)</h5>
                        
                        <div class="row g-3">
                            <!-- Judul Tentang Portal -->
                            <div class="col-md-12">
                                <label for="landing_about_title" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Judul "Tentang Portal"</label>
                                <input type="text" id="landing_about_title" name="landing_about_title" class="form-control" value="<?= htmlspecialchars($settings['landing_about_title'] ?? 'Tentang Portal') ?>" required autocomplete="off">
                            </div>

                            <!-- Paragraf Deskripsi 1 -->
                            <div class="col-md-12">
                                <label for="landing_about_desc1" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Paragraf Deskripsi 1</label>
                                <textarea id="landing_about_desc1" name="landing_about_desc1" class="form-control" rows="3" required><?= htmlspecialchars($settings['landing_about_desc1'] ?? '') ?></textarea>
                            </div>

                            <!-- Paragraf Deskripsi 2 -->
                            <div class="col-md-12">
                                <label for="landing_about_desc2" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Paragraf Deskripsi 2</label>
                                <textarea id="landing_about_desc2" name="landing_about_desc2" class="form-control" rows="3"><?= htmlspecialchars($settings['landing_about_desc2'] ?? '') ?></textarea>
                            </div>

                            <!-- Upload Gambar -->
                            <div class="col-md-6">
                                <label for="landing_image" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Upload Gambar Utama Portal</label>
                                <input type="file" id="landing_image" name="landing_image" class="form-control" accept="image/*">
                                <small class="text-white-50 mt-1 d-block" style="font-size: 0.72rem;">*Format: JPG/PNG/WebP, Max 5MB. Biarkan kosong jika tidak ingin diubah.</small>
                            </div>

                            <!-- Preview Gambar -->
                            <div class="col-md-6 d-flex align-items-center">
                                <?php 
                                $previewUrl = !empty($settings['landing_image']) ? APP_URL . '/public/uploads/' . $settings['landing_image'] : 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=600&q=80';
                                ?>
                                <div class="text-center w-100 p-2 border border-secondary border-opacity-25 rounded-3" style="background: rgba(0,0,0,0.1)">
                                    <div class="text-white-50 small mb-1" style="font-size: 0.72rem;">Preview Gambar Saat Ini:</div>
                                    <img src="<?= $previewUrl ?>" class="img-fluid rounded-2" style="max-height: 120px; max-width: 100%; height: auto; object-fit: contain;">
                                </div>
                            </div>

                            <!-- Upload Background Image -->
                            <div class="col-md-6 mt-3">
                                <label for="landing_bg_image" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Upload Foto Background Utama (Landing Page)</label>
                                <input type="file" id="landing_bg_image" name="landing_bg_image" class="form-control" accept="image/*">
                                <small class="text-white-50 mt-1 d-block" style="font-size: 0.72rem;">*Format: JPG/PNG/WebP, Max 5MB. Biarkan kosong jika tidak ingin diubah.</small>
                            </div>

                            <!-- Preview Background Image -->
                            <div class="col-md-6 mt-3 d-flex align-items-center">
                                <?php 
                                $previewBgUrl = !empty($settings['landing_bg_image']) ? APP_URL . '/public/uploads/' . $settings['landing_bg_image'] : 'https://images.unsplash.com/photo-1605001011156-cbf0b0f67a51?auto=format&fit=crop&w=1920&q=80';
                                ?>
                                <div class="text-center w-100 p-2 border border-secondary border-opacity-25 rounded-3" style="background: rgba(0,0,0,0.1)">
                                    <div class="text-white-50 small mb-1" style="font-size: 0.72rem;">Preview Background Saat Ini:</div>
                                    <img src="<?= $previewBgUrl ?>" class="img-fluid rounded-2" style="max-height: 120px; max-width: 100%; height: auto; object-fit: contain;">
                                </div>
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
