<?php $pageTitle = 'Backup Database'; ?>

<?php ob_start(); ?>

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex gap-2 rounded-3 mb-4 border-0 text-white" style="background: rgba(var(--bs-<?= $flash['type'] ?>-rgb), 0.2); backdrop-filter: blur(10px);" role="alert">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Status & Info Card -->
    <div class="col-lg-4">
        <div class="card border-0 mb-4 h-100">
            <div class="card-header bg-transparent py-3 border-0">
                <span class="fs-6 fw-bold text-white"><i class="bi bi-info-circle-fill me-2 text-info"></i>Status Database</span>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                        <span class="text-white-50" style="font-size: 0.88rem;">Nama Database:</span>
                        <code class="text-gradient fw-bold" style="font-size: 0.88rem;"><?= htmlspecialchars($dbName) ?></code>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                        <span class="text-white-50" style="font-size: 0.88rem;">Jumlah Tabel:</span>
                        <span class="text-white fw-bold" style="font-size: 0.88rem;"><?= number_format($tableCount) ?> Tabel</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-2">
                        <span class="text-white-50" style="font-size: 0.88rem;">Total Ukuran Data:</span>
                        <span class="text-white fw-bold" style="font-size: 0.88rem;">
                            <?php 
                            if ($dbSizeBytes >= 1048576) {
                                echo number_format($dbSizeBytes / 1048576, 2) . ' MB';
                            } else {
                                echo number_format($dbSizeBytes / 1024, 2) . ' KB';
                            }
                            ?>
                        </span>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <form action="<?= APP_URL ?>/superadmin/backup/create" method="POST" id="backupForm">
                        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold" id="btnRunBackup">
                            <i class="bi bi-database-fill-up me-2"></i> Buat Backup Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup List Card -->
    <div class="col-lg-8">
        <div class="card border-0 h-100">
            <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
                <span class="fs-6 fw-bold text-white"><i class="bi bi-clock-history me-2 text-warning"></i>Riwayat File Backup</span>
                <span class="badge bg-secondary-subtle text-white-50" style="font-size: 0.7rem;">Folder: /database/backups/</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Nama File</th>
                                <th>Ukuran</th>
                                <th>Tanggal Dibuat</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($files as $file): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-filetype-sql fs-4 text-accent"></i>
                                        <span class="fw-semibold text-white" style="font-size: 0.85rem;"><?= htmlspecialchars($file['name']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?php 
                                        if ($file['size'] >= 1048576) {
                                            echo number_format($file['size'] / 1048576, 2) . ' MB';
                                        } else {
                                            echo number_format($file['size'] / 1024, 2) . ' KB';
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted" style="font-size: 0.8rem;"><?= date('d/m/Y H:i:s', $file['created_at']) ?></small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <a href="<?= APP_URL ?>/superadmin/backup/download?file=<?= urlencode($file['name']) ?>" class="btn btn-sm btn-outline-success" title="Unduh Backup" style="border-radius: 8px;">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-backup" data-file="<?= htmlspecialchars($file['name']) ?>" title="Hapus Backup" style="border-radius: 8px;">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($files)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-database-exclamation fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada file backup database.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$footerExtra = <<<JS
<script>
$(document).ready(function() {
    // Run Backup Loading effect
    $('#backupForm').on('submit', function() {
        const btn = $('#btnRunBackup');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Membuat Backup...');
    });

    // Delete Backup File
    $('.btn-delete-backup').on('click', function() {
        const file = $(this).data('file');

        Swal.fire({
            title: 'Hapus File Backup?',
            text: "File '" + file + "' akan dihapus permanen dari server.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#334155',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(APP_URL + '/superadmin/backup/delete', {
                    _csrf_token: '{$csrfToken}',
                    file: file
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
