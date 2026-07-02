<?php $pageTitle = 'Registrasi Akun Baru'; ?>
<?php ob_start(); ?>

<div class="row justify-content-center" data-aos="fade-up">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
            <div class="position-absolute start-0 top-0 w-100 h-1.5 bg-success"></div>
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill text-success"></i> Registrasi Akun Baru
                </h5>
                <p class="text-muted small mb-0">
                    Membuat akun login sekaligus data kependudukan untuk warga/pengguna baru.
                </p>
            </div>

            <div class="card-body p-4 pt-1">
                <form method="POST" action="<?= APP_URL ?>/admin/user/store" id="createUserForm">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <!-- Segment 1: Akun Login -->
                    <div class="border-bottom pb-3 mb-4">
                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-key-fill me-1.5"></i> Akun Login</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Peran (Role) <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select" style="border-radius:10px;">
                                    <option value="1" selected>Warga</option>
                                    <option value="2">Admin Desa</option>
                                    <option value="3">Kepala Desa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" placeholder="cth: budi_santoso" required style="border-radius:10px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required style="border-radius:10px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8" style="border-radius:10px;">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Sampaikan username & password ini langsung ke warga yang bersangkutan.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Segment 2: Identitas Kependudukan -->
                    <div class="border-bottom pb-3 mb-4">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-card-list me-1.5"></i> Identitas Kependudukan</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" required pattern="\d{16}" maxlength="16" style="border-radius:10px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">No. Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                                <input type="text" name="no_kk" class="form-control" placeholder="16 digit No. KK" required pattern="\d{16}" maxlength="16" style="border-radius:10px;">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama sesuai KTP" required style="border-radius:10px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control" required style="border-radius:10px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control" required style="border-radius:10px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" style="border-radius:10px;">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Agama <span class="text-danger">*</span></label>
                                <select name="agama" class="form-select" style="border-radius:10px;">
                                    <?php foreach (['Islam','Kristen','Katolik','Hindu','Budha','Konghucu','Lainnya'] as $agama): ?>
                                    <option value="<?= $agama ?>"><?= $agama ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">No. HP</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" style="border-radius:10px;">
                            </div>
                        </div>
                    </div>

                    <!-- Segment 3: Alamat -->
                    <div class="pb-2 mb-4">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-geo-alt-fill me-1.5"></i> Alamat</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-secondary">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="2" required style="border-radius:10px;"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">RT <span class="text-danger">*</span></label>
                                <input type="text" name="rt" class="form-control" placeholder="001" required style="border-radius:10px;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">RW <span class="text-danger">*</span></label>
                                <input type="text" name="rw" class="form-control" placeholder="001" required style="border-radius:10px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= APP_URL ?>/admin/user" class="btn btn-light px-4" style="border-radius:10px;">Batal</a>
                        <button type="submit" class="btn btn-success px-4" style="border-radius:10px;">
                            <i class="bi bi-check2-circle me-1"></i> Buat Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
