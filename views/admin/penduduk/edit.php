<?php $pageTitle = 'Edit Penduduk'; ?>
<?php ob_start(); ?>

<div class="row justify-content-center" data-aos="fade-up">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
            <div class="position-absolute start-0 top-0 w-100 h-1.5 bg-warning"></div>
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-warning"></i> Edit Data Penduduk
                </h5>
                <p class="text-muted small mb-0">Ubah data penduduk yang diperlukan, lalu simpan perubahan.</p>
            </div>

            <div class="card-body p-4 pt-1">
                <form method="POST" action="<?= APP_URL ?>/admin/penduduk/update/<?= $penduduk['id'] ?>" id="editPendudukForm">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <!-- Segment 1: Identitas Utama -->
                    <div class="border-bottom pb-3 mb-4">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-card-list me-1.5"></i> Identitas Utama</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                                <input type="text" name="nik" value="<?= htmlspecialchars($penduduk['nik']) ?>" class="form-control" placeholder="16 digit NIK" required pattern="\d{16}" maxlength="16" style="border-radius:10px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Nomor Kartu Keluarga (No. KK) <span class="text-danger">*</span></label>
                                <input type="text" name="no_kk" value="<?= htmlspecialchars($penduduk['no_kk']) ?>" class="form-control" placeholder="16 digit No. KK" required pattern="\d{16}" maxlength="16" style="border-radius:10px;">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($penduduk['nama']) ?>" class="form-control" placeholder="Nama sesuai KTP" required style="border-radius:10px;">
                            </div>
                        </div>
                    </div>

                    <!-- Segment 2: Informasi Pribadi -->
                    <div class="border-bottom pb-3 mb-4">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-1.5"></i> Informasi Pribadi</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($penduduk['tempat_lahir']) ?>" class="form-control" placeholder="Kota / Kabupaten" required style="border-radius:10px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($penduduk['tanggal_lahir']) ?>" class="form-control" required style="border-radius:10px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" style="border-radius:10px;">
                                    <option value="L" <?= $penduduk['jenis_kelamin']==='L'?'selected':'' ?>>Laki-laki</option>
                                    <option value="P" <?= $penduduk['jenis_kelamin']==='P'?'selected':'' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Agama <span class="text-danger">*</span></label>
                                <select name="agama" class="form-select" style="border-radius:10px;">
                                    <?php foreach(['Islam','Protestan','Katolik','Hindu','Buddha','Khonghucu','Lainnya'] as $agama): ?>
                                    <option value="<?= $agama ?>" <?= $penduduk['agama']===$agama?'selected':'' ?>><?= $agama ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Status Perkawinan <span class="text-danger">*</span></label>
                                <select name="status_kawin" class="form-select" style="border-radius:10px;">
                                    <?php foreach(['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $penduduk['status_kawin']===$status?'selected':'' ?>><?= $status ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Pendidikan <span class="text-danger">*</span></label>
                                <select name="pendidikan" class="form-select" style="border-radius:10px;">
                                    <?php foreach(['Tidak/Belum Sekolah','SD','SMP','SMA','Diploma','Sarjana','Pascasarjana'] as $pend): ?>
                                    <option value="<?= $pend ?>" <?= $penduduk['pendidikan']===$pend?'selected':'' ?>><?= $pend ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" name="pekerjaan" value="<?= htmlspecialchars($penduduk['pekerjaan']) ?>" class="form-control" placeholder="Pekerjaan saat ini" required style="border-radius:10px;">
                            </div>
                        </div>
                    </div>

                    <!-- Segment 3: Kontak & Domisili -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-geo-alt me-1.5"></i> Kontak & Domisili</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Nomor Handphone (HP)</label>
                                <input type="text" name="no_hp" value="<?= htmlspecialchars($penduduk['no_hp']) ?>" class="form-control" placeholder="08xxxxxxxxxx" style="border-radius:10px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Status Penduduk <span class="text-danger">*</span></label>
                                <select name="status_penduduk" class="form-select" style="border-radius:10px;">
                                    <?php foreach(['Tetap','Sementara','Pindah','Meninggal'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $penduduk['status_penduduk']===$status?'selected':'' ?>><?= $status ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-secondary">Alamat Rumah <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="2" placeholder="Nama Jalan, Blok, No. Rumah" required style="border-radius:10px;"><?= htmlspecialchars($penduduk['alamat']) ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">RT <span class="text-danger">*</span></label>
                                <input type="text" name="rt" value="<?= htmlspecialchars($penduduk['rt']) ?>" class="form-control" placeholder="Tiga digit (contoh: 001)" required pattern="\d{3}" maxlength="3" style="border-radius:10px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">RW <span class="text-danger">*</span></label>
                                <input type="text" name="rw" value="<?= htmlspecialchars($penduduk['rw']) ?>" class="form-control" placeholder="Tiga digit (contoh: 002)" required pattern="\d{3}" maxlength="3" style="border-radius:10px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Dusun <span class="text-danger">*</span></label>
                                <input type="text" name="dusun" value="<?= htmlspecialchars($penduduk['dusun']) ?>" class="form-control" placeholder="Nama Dusun" required style="border-radius:10px;">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                        <a href="<?= APP_URL ?>/admin/penduduk" class="btn btn-light px-4 py-2" style="border-radius:10px;">
                            <i class="bi bi-arrow-left me-1.5"></i> Batal
                        </a>
                        <button type="submit" id="submitBtn" class="btn btn-warning text-dark px-4 py-2" style="border-radius:10px; font-weight: 600;">
                            <i class="bi bi-save me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$footerExtra = <<<JS
<script>
document.getElementById('editPendudukForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    btn.disabled = true;
});
</script>
JS;
?>
<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
