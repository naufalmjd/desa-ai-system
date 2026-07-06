<?php $pageTitle = 'Pengaturan Profil'; ?>

<?php ob_start(); ?>

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex gap-2 rounded-3 mb-4 border-0 text-white" style="background: rgba(var(--bs-<?= $flash['type'] ?>-rgb), 0.2); backdrop-filter: blur(10px);" role="alert">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Update Profile Form -->
    <div class="col-lg-6">
        <div class="card border-0 h-100">
            <div class="card-header bg-transparent py-3 border-0">
                <span class="fs-6 fw-bold text-white"><i class="bi bi-person-bounding-box me-2 text-primary"></i>Informasi Profil</span>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/superadmin/profil/update" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="mb-3">
                        <label for="username" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username..." value="<?= htmlspecialchars($user['username']) ?>" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email..." value="<?= htmlspecialchars($user['email']) ?>" required autocomplete="off">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2.5">
                            <i class="bi bi-check-circle me-1"></i> Perbarui Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Form -->
    <div class="col-lg-6">
        <div class="card border-0 h-100">
            <div class="card-header bg-transparent py-3 border-0">
                <span class="fs-6 fw-bold text-white"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>Ganti Kata Sandi</span>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/superadmin/profil/changePassword" method="POST" id="passwordForm">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="mb-3">
                        <label for="old_password" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Kata Sandi Lama</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" id="old_password" name="old_password" class="form-control" placeholder="Masukkan kata sandi lama..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Kata Sandi Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-key-fill"></i></span>
                            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Masukkan kata sandi baru..." required>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Minimal 6 karakter.</small>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Konfirmasi Kata Sandi Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-check-all"></i></span>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ulangi kata sandi baru..." required>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-warning py-2.5 text-white fw-bold border-0" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 15px rgba(245, 158, 11, 0.25);">
                            <i class="bi bi-shield-check me-1"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$footerExtra = <<<JS
<script>
$(document).ready(function() {
    $('#passwordForm').on('submit', function(e) {
        const newPass = $('#new_password').val();
        const confirmPass = $('#confirm_password').val();

        if (newPass.length < 6) {
            e.preventDefault();
            Swal.fire('Oops!', 'Password baru minimal 6 karakter.', 'warning');
            return false;
        }

        if (newPass !== confirmPass) {
            e.preventDefault();
            Swal.fire('Oops!', 'Konfirmasi password baru tidak cocok.', 'warning');
            return false;
        }
    });
});
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/superadmin.php'; ?>
