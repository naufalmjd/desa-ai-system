<?php $pageTitle = 'Kelola Akun Pengguna'; ?>
<?php ob_start(); ?>

<div data-aos="fade-up">
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Kelola Akun Pengguna</h5>
            <p class="text-muted small mb-0">Daftar seluruh akun login (Warga, Admin, Kepala Desa).</p>
        </div>
        <a href="<?= APP_URL ?>/admin/user/create" class="btn btn-success" style="border-radius:10px;">
            <i class="bi bi-person-plus me-1"></i> Registrasi Akun Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($result['data'])): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada akun terdaftar.</td></tr>
                    <?php else: foreach ($result['data'] as $u): ?>
                        <tr>
                            <td class="ps-4 fw-semibold"><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($u['role_label'] ?? $u['role_name']) ?></span></td>
                            <td>
                                <?php if ($u['is_active']): ?>
                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= $u['last_login_at'] ? date('d M Y H:i', strtotime($u['last_login_at'])) : '—' ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
