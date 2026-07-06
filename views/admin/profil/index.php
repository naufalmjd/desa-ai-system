<?php
$pageTitle = 'Profil Admin';
ob_start();
?>

<div class="row g-4" data-aos="fade-up">
    <!-- Profil Card -->
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                 style="width: 80px; height: 80px; font-size: 2.2rem">
                <?= strtoupper(substr($user['nama'] ?? 'A', 0, 2)) ?>
            </div>
            <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user['nama'] ?? '') ?></h5>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 mb-3" style="font-size: .75rem">
                <?= strtoupper($user['role'] ?? 'Admin') ?>
            </span>
            <hr class="w-100 my-3">
            <div class="text-start">
                <small class="text-muted d-block small">Username</small>
                <span class="text-dark fw-semibold mb-2 d-block"><?= htmlspecialchars($user['username'] ?? '') ?></span>
                
                <small class="text-muted d-block small">Alamat Email</small>
                <span class="text-dark fw-semibold mb-2 d-block"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                
                <small class="text-muted d-block small">Jabatan</small>
                <span class="text-dark fw-semibold d-block">Staf Administrasi Desa</span>
            </div>
            <!-- Tombol Edit Profil -->
            <button class="btn btn-primary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#editProfilModal">
                <i class="bi bi-pencil-square me-1"></i> Edit Profil
            </button>
        </div>
    </div>

    <!-- Data Penduduk (tetap) -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-person-badge-fill me-2 text-primary"></i>Data Pegawai
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
                        <strong class="text-dark"><?= htmlspecialchars($penduduk['alamat'] ?? '—') ?>, RT <?= htmlspecialchars($penduduk['rt'] ?? '—') ?>/RW <?= htmlspecialchars($penduduk['rw'] ?? '—') ?>, <?= htmlspecialchars($penduduk['dusun'] ?? '—') ?>, Kec. <?= htmlspecialchars($penduduk['kecamatan'] ?? '—') ?>, <?= htmlspecialchars($penduduk['kabupaten'] ?? '—') ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL EDIT PROFIL ========== -->
<div class="modal fade" id="editProfilModal" tabindex="-1" aria-labelledby="editProfilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editProfilModalLabel"><i class="bi bi-person-gear me-2"></i>Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form method="POST" action="<?= APP_URL ?>/admin/profil/update" id="formEditProfil">
                <div class="modal-body">
                    <!-- CSRF Token (sesuaikan dengan mekanisme Anda) -->
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="edit_username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username"
                               value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="edit_email" class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email"
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <hr>

                    <!-- Password Baru (opsional) -->
                    <div class="mb-3">
                        <label for="edit_password" class="form-label fw-semibold">Password Baru <span class="text-muted small">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" class="form-control" id="edit_password" name="password" placeholder="Masukkan password baru">
                    </div>
                    <div class="mb-3">
                        <label for="edit_password_confirm" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="edit_password_confirm" name="password_confirm" placeholder="Ulangi password baru">
                    </div>

                    <!-- Pesan error (bisa ditampilkan dari flash message) -->
                    <?php if (isset($flash) && $flash['type'] === 'danger'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($flash['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script untuk validasi password dan tampilkan modal jika ada error -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Jika ada error dari server, tampilkan modal otomatis
    <?php if (isset($flash) && $flash['type'] === 'danger'): ?>
        var myModal = new bootstrap.Modal(document.getElementById('editProfilModal'));
        myModal.show();
    <?php endif; ?>

    // Validasi sederhana: password dan konfirmasi harus sama jika diisi
    document.getElementById('formEditProfil').addEventListener('submit', function(e) {
        var pass = document.getElementById('edit_password').value;
        var confirm = document.getElementById('edit_password_confirm').value;
        if (pass !== '' && pass !== confirm) {
            e.preventDefault();
            alert('Password baru dan konfirmasi tidak cocok!');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/admin.php';
?>