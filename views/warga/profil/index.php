<?php $pageTitle = 'Profil Saya'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Profil Card -->
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                 style="width: 80px; height: 80px; font-size: 2.2rem">
                <?= strtoupper(substr($user['nama'] ?? 'U', 0, 2)) ?>
            </div>
            <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user['nama'] ?? '') ?></h5>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mb-3" style="font-size: .75rem">
                <?= strtoupper($user['role'] ?? 'Warga') ?>
            </span>
            <hr class="w-100 my-3">
            <div class="text-start">
                <small class="text-muted d-block small">Username</small>
                <span class="text-dark fw-semibold mb-2 d-block"><?= htmlspecialchars($user['username'] ?? '') ?></span>

                <small class="text-muted d-block small">Alamat Email</small>
                <span class="text-dark fw-semibold mb-2 d-block"><?= htmlspecialchars($user['email'] ?? '') ?></span>

                <small class="text-muted d-block small">Terdaftar Sejak</small>
                <span class="text-dark fw-semibold d-block"><?= date('d F Y', $user['login_at'] ?? time()) ?></span>
            </div>
        </div>
    </div>

    <div class="col-lg-8">

        <!-- Form Edit Profil -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profil
            </div>
            <div class="card-body">
                <?php if (!$penduduk): ?>
                    <div class="alert alert-warning mb-3">
                        Data kependudukan Anda belum terhubung. Hubungi Admin Desa untuk menautkan akun Anda.
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= APP_URL ?>/warga/profil/update">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small text-muted mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control"
                                   value="<?= htmlspecialchars($penduduk['nama'] ?? $user['nama'] ?? '') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small text-muted mb-1">Alamat Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small text-muted mb-1">No. HP / Telepon</label>
                            <input type="text" name="no_hp" class="form-control"
                                   value="<?= htmlspecialchars($penduduk['no_hp'] ?? '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small text-muted mb-1">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control"
                                   value="<?= htmlspecialchars($penduduk['pekerjaan'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="text-muted small mt-3 mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        NIK, No. KK, Tempat/Tanggal Lahir, dan Alamat KTP hanya bisa diubah oleh Admin Desa
                        karena merupakan data resmi kependudukan.
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2 me-1"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Data Penduduk (read-only) -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-person-badge-fill me-2 text-primary"></i>Data Kependudukan Resmi Anda
            </div>
            <div class="card-body">
                <div class="row g-3" style="font-size: .85rem">
                    <div class="col-sm-6 border-bottom pb-2">
                        <small class="text-muted d-block mb-1">Nomor Induk Kependudukan (NIK)</small>
                        <strong class="text-primary font-monospace"><?= htmlspecialchars($penduduk['nik'] ?? '—') ?></strong>
                    </div>
                    <div class="col-sm-6 border-bottom pb-2">
                        <small class="text-muted d-block mb-1">Nomor Kartu Keluarga (KK)</small>
                        <strong class="text-dark font-monospace"><?= htmlspecialchars($penduduk['no_kk'] ?? '—') ?></strong>
                    </div>
                    <div class="col-sm-6 border-bottom pb-2">
                        <small class="text-muted d-block mb-1">Tempat, Tanggal Lahir</small>
                        <strong class="text-dark"><?= htmlspecialchars($penduduk['tempat_lahir'] ?? '—') ?>, <?= isset($penduduk['tanggal_lahir']) ? date('d/m/Y', strtotime($penduduk['tanggal_lahir'])) : '—' ?></strong>
                    </div>
                    <div class="col-sm-6 border-bottom pb-2">
                        <small class="text-muted d-block mb-1">Jenis Kelamin</small>
                        <strong class="text-dark"><?= ($penduduk['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' ?></strong>
                    </div>
                    <div class="col-sm-6 border-bottom pb-2">
                        <small class="text-muted d-block mb-1">Agama</small>
                        <strong class="text-dark"><?= htmlspecialchars($penduduk['agama'] ?? '—') ?></strong>
                    </div>
                    <div class="col-sm-6 border-bottom pb-2">
                        <small class="text-muted d-block mb-1">Pendidikan Terakhir</small>
                        <strong class="text-dark"><?= htmlspecialchars($penduduk['pendidikan'] ?? '—') ?></strong>
                    </div>
                    <div class="col-sm-6 border-bottom pb-2">
                        <small class="text-muted d-block mb-1">Pekerjaan</small>
                        <strong class="text-dark"><?= htmlspecialchars($penduduk['pekerjaan'] ?? '—') ?></strong>
                    </div>
                    <div class="col-sm-6 border-bottom pb-2">
                        <small class="text-muted d-block mb-1">No. HP / Telepon</small>
                        <strong class="text-dark"><?= htmlspecialchars($penduduk['no_hp'] ?? '—') ?></strong>
                    </div>
                    <div class="col-12 pt-2">
                        <small class="text-muted d-block mb-1">Alamat Lengkap KTP</small>
                        <strong class="text-dark"><?= htmlspecialchars($penduduk['alamat'] ?? '—') ?>, RT <?= htmlspecialchars($penduduk['rt'] ?? '—') ?>/RW <?= htmlspecialchars($penduduk['rw'] ?? '—') ?>, <?= htmlspecialchars($penduduk['dusun'] ?? '—') ?>, Kec. <?= htmlspecialchars($penduduk['kecamatan'] ?? '—') ?>, <?= htmlspecialchars($penduduk['kabupaten'] ?? '—') ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
