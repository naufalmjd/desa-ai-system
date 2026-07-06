<?php $pageTitle = 'Tambah Pengguna Baru'; ?>

<?php ob_start(); ?>

<div class="row justify-content-center" data-aos="fade-up">
    <div class="col-lg-8">
        <div class="card border-0">
            <div class="card-header bg-transparent py-3 border-0 d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/superadmin/user" class="btn btn-sm btn-outline-secondary p-1 border-0" style="border-radius: 8px;"><i class="bi bi-arrow-left fs-5 text-white"></i></a>
                <span class="fs-6 fw-bold text-white">Formulir Pendaftaran Pengguna</span>
            </div>
            <div class="card-body p-4">
                <form action="<?= APP_URL ?>/superadmin/user/store" method="POST" id="userForm">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username..." required autofocus autocomplete="off">
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Hanya huruf, angka, dan underscore, minimal 3 karakter.</small>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email..." required autocomplete="off">
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Pastikan email unik dan valid.</small>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Kata Sandi (Password)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi..." required autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="bi bi-eye-slash"></i></button>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Minimal 6 karakter, disarankan kombinasi huruf & angka.</small>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Role -->
                        <div class="col-md-6">
                            <label for="role_id" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Peran Pengguna (Role)</label>
                            <select id="role_id" name="role_id" class="form-select" required>
                                <option value="" disabled selected>Pilih Peran...</option>
                                <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="is_active" class="form-label text-white-50 fw-semibold" style="font-size: 0.82rem;">Status Akun</label>
                            <select id="is_active" name="is_active" class="form-select" required>
                                <option value="1" selected>Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <hr class="border-secondary mb-4 opacity-10">

                    <!-- Actions -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= APP_URL ?>/superadmin/user" class="btn btn-outline-secondary px-4 py-2" style="border-color: rgba(255,255,255,0.1); color: var(--text-muted);">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-check-circle me-1"></i> Simpan User
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
    // Show/hide password
    $('#togglePassword').on('click', function() {
        const passwordInput = $('#password');
        const icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    // Form Client Validation
    $('#userForm').on('submit', function(e) {
        const username = $('#username').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        
        if (username.length < 3) {
            e.preventDefault();
            Swal.fire('Oops!', 'Username minimal 3 karakter.', 'warning');
            return false;
        }

        if (password.length < 6) {
            e.preventDefault();
            Swal.fire('Oops!', 'Password minimal 6 karakter.', 'warning');
            return false;
        }
    });
});
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/superadmin.php'; ?>
