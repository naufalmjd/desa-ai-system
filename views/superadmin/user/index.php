<?php 
$pageTitle = 'Kelola Pengguna'; 
$user = $_SESSION['user'] ?? null;
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>

<?php ob_start(); ?>

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex gap-2 rounded-3 mb-4 border-0 text-white" style="background: rgba(var(--bs-<?= $flash['type'] ?>-rgb), 0.2); backdrop-filter: blur(10px);" role="alert">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Header / Search and Actions -->
<div class="card p-3 mb-4 border-0" data-aos="fade-up">
    <form action="" method="GET" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-secondary text-muted" style="border-right: none !important;"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Cari username atau email..." value="<?= htmlspecialchars($filters['search']) ?>" style="border-left: none !important;">
            </div>
        </div>
        <div class="col-md-3">
            <select name="role_id" class="form-select">
                <option value="">Semua Peran</option>
                <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $filters['role_id'] === (int)$r['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['label']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary py-2">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
        </div>
        <div class="col-md-2 d-grid">
            <a href="<?= APP_URL ?>/superadmin/user/create" class="btn btn-success py-2 border-0" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 600; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);">
                <i class="bi bi-plus-lg me-1"></i> Tambah User
            </a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card border-0 overflow-hidden" data-aos="fade-up" data-aos-delay="50">
    <div class="card-header bg-transparent py-3 border-0">
        <span class="fs-6 fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i>Daftar Pengguna Sistem</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>User Utama</th>
                        <th>Email</th>
                        <th>Peran (Role)</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = ($result['current_page'] - 1) * $result['per_page'] + 1;
                    foreach ($result['data'] as $u): 
                    ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $no++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem; color: #fff !important;">
                                    <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                </div>
                                <div class="fw-semibold text-white"><?= htmlspecialchars($u['username']) ?></div>
                            </div>
                        </td>
                        <td><span style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></span></td>
                        <td>
                            <span class="badge-role <?= htmlspecialchars($u['role_name']) ?>">
                                <?= htmlspecialchars($u['role_label']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="form-check form-switch m-0 p-0 d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0 toggle-status-switch" type="checkbox" role="switch" 
                                       data-id="<?= $u['id'] ?>" <?= $u['is_active'] ? 'checked' : '' ?> 
                                       <?= $u['id'] === $user['id'] ? 'disabled' : '' ?>
                                       style="cursor: pointer; width: 2.2em; height: 1.1em;">
                                <span class="badge bg-<?= $u['is_active'] ? 'success' : 'danger' ?>-subtle text-<?= $u['is_active'] ? 'success' : 'danger' ?> border-0 text-uppercase" style="font-size: 0.65rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 5px;" id="status-badge-<?= $u['id'] ?>">
                                    <?= $u['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php if ($u['last_login_at']): ?>
                            <div style="font-size: 0.78rem;">
                                <span class="text-white d-block"><?= date('d/m/Y H:i', strtotime($u['last_login_at'])) ?></span>
                                <small class="text-muted"><?= htmlspecialchars($u['last_login_ip'] ?? '') ?></small>
                            </div>
                            <?php else: ?>
                            <span class="text-muted" style="font-size: 0.78rem;">Belum pernah</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-2">
                                <a href="<?= APP_URL ?>/superadmin/user/edit/<?= $u['id'] ?>" class="btn btn-sm btn-outline-info" title="Edit User" style="border-radius: 8px;">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <?php if ($u['id'] !== $user['id']): ?>
                                <button class="btn btn-sm btn-outline-danger btn-delete-user" data-id="<?= $u['id'] ?>" data-name="<?= htmlspecialchars($u['username']) ?>" title="Hapus User" style="border-radius: 8px;">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($result['data'])): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-search fs-2 d-block mb-2 text-secondary"></i>
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($result['last_page'] > 1): ?>
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3 border-0" style="border-top: 1px solid rgba(255,255,255,0.04) !important;">
        <small class="text-muted">
            Menampilkan <?= count($result['data']) ?> dari <?= number_format($result['total']) ?> pengguna
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $result['last_page']; $i++): ?>
                <li class="page-item <?= $i === $result['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($filters['search']) ?>&role_id=<?= $filters['role_id'] ?>" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.05); color: var(--text-muted);">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$footerExtra = <<<JS
<script>
$(document).ready(function() {
    // Styling active state of custom pagination links
    $('.pagination .active a').css({
        'background': 'linear-gradient(135deg, var(--primary), var(--primary-dark))',
        'border-color': 'var(--primary)',
        'color': '#fff'
    });

    // Toggle Status
    $('.toggle-status-switch').on('change', function() {
        const userId = $(this).data('id');
        const switchEl = $(this);
        const badgeEl = $('#status-badge-' + userId);

        $.post(APP_URL + '/superadmin/user/toggleStatus/' + userId, {
            _csrf_token: '{$csrfToken}'
        }, function(res) {
            if (res.success) {
                if (res.data.is_active) {
                    badgeEl.removeClass('bg-danger-subtle text-danger').addClass('bg-success-subtle text-success').text('Aktif');
                    switchEl.prop('checked', true);
                } else {
                    badgeEl.removeClass('bg-success-subtle text-success').addClass('bg-danger-subtle text-danger').text('Nonaktif');
                    switchEl.prop('checked', false);
                }
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message,
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                switchEl.prop('checked', !switchEl.is(':checked'));
                Swal.fire('Gagal!', res.message, 'error');
            }
        }).fail(function() {
            switchEl.prop('checked', !switchEl.is(':checked'));
            Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
        });
    });

    // Delete User
    $('.btn-delete-user').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Pengguna?',
            text: "Akun '" + name + "' akan dihapus secara lunak (soft delete). Akun ini tidak akan dapat login lagi.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#334155',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(APP_URL + '/superadmin/user/delete/' + id, {
                    _csrf_token: '{$csrfToken}'
                }, function(res) {
                    if (res.success) {
                        Swal.fire('Terhapus!', res.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                });
            }
        });
    });
});
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/superadmin.php'; ?>
