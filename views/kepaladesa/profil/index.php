<?php $pageTitle = 'Profil Kepala Desa'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Profil Card -->
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <div class="bg-warning rounded-circle text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                 style="width: 80px; height: 80px; font-size: 2.2rem; background: linear-gradient(135deg, #d97706, #b45309) !important">
                <?= strtoupper(substr($user['nama'] ?? 'K', 0, 2)) ?>
            </div>
            <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user['nama'] ?? '') ?></h5>
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 mb-3" style="font-size: .75rem">
                <?= strtoupper($user['role'] ?? 'Kepala Desa') ?>
            </span>
            <hr class="w-100 my-3">
            <div class="text-start">
                <small class="text-muted d-block small">Username</small>
                <span class="text-dark fw-semibold mb-2 d-block"><?= htmlspecialchars($user['username'] ?? '') ?></span>

                <small class="text-muted d-block small">Alamat Email</small>
                <span class="text-dark fw-semibold mb-2 d-block"><?= htmlspecialchars($user['email'] ?? '') ?></span>

                <small class="text-muted d-block small">Jabatan Eksekutif</small>
                <span class="text-dark fw-semibold d-block">Kepala Desa Sukamaju</span>
            </div>
        </div>
    </div>

    <div class="col-lg-8">

        <!-- Form Edit Profil -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Profil
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/kepaladesa/profil/update">
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
                    </div>

                    <button type="submit" class="btn btn-warning text-white px-4 mt-3">
                        <i class="bi bi-check2 me-1"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Data Jabatan & Kependudukan (read-only) -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-person-badge-fill me-2 text-warning"></i>Data Jabatan & Kependudukan Resmi
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
                    <div class="col-12 pt-2">
                        <small class="text-muted d-block mb-1">Alamat Lengkap</small>
                        <strong class="text-dark"><?= htmlspecialchars($penduduk['alamat'] ?? '—') ?>, RT <?= htmlspecialchars($penduduk['rt'] ?? '—') ?>/RW <?= htmlspecialchars($penduduk['rw'] ?? '—') ?> Dusun <?= htmlspecialchars($penduduk['dusun'] ?? '—') ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/kepaladesa.php'; ?>
