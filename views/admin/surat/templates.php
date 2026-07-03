<?php $pageTitle = 'Kelola Template Surat'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card" data-aos="fade-up">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <span><i class="bi bi-file-earmark-text-fill me-2 text-primary"></i>Daftar Template Surat Administrasi</span>
        <a href="<?= APP_URL ?>/admin/surat/templatecreate" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Template Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px">Kode</th>
                        <th>Nama Surat</th>
                        <th>Deskripsi</th>
                        <th>Persyaratan Berkas</th>
                        <th style="width: 120px">Estimasi Kerja</th>
                        <th>Berkas Template</th>
                        <th style="width: 100px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $t): ?>
                    <tr>
                        <td><span class="badge bg-secondary font-monospace"><?= htmlspecialchars($t['kode']) ?></span></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($t['nama']) ?></td>
                        <td class="text-muted" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= htmlspecialchars($t['deskripsi'] ?? '—') ?>
                        </td>
                        <td>
                            <?php 
                            $syarat = json_decode($t['persyaratan'] ?? '[]', true);
                            if (empty($syarat)): 
                            ?>
                                <span class="text-muted small">Tidak ada</span>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach ($syarat as $s): ?>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: .65rem">
                                            <?= htmlspecialchars($s) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><span class="text-dark fw-medium"><?= (int)$t['estimasi_hari'] ?> Hari</span></td>
                        <td>
                            <?php if (!empty($t['template_path'])): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="<?= APP_URL ?><?= htmlspecialchars($t['template_path']) ?>" target="_blank" class="btn btn-xs btn-outline-success py-1 px-2" style="font-size: .75rem" title="Unduh File Template">
                                        <i class="bi bi-file-earmark-arrow-down-fill"></i> Unduh
                                    </a>
                                    <!-- Form to change file template -->
                                    <form method="POST" action="<?= APP_URL ?>/admin/surat/templateupload/<?= $t['id'] ?>" enctype="multipart/form-data" class="d-inline-block m-0">
                                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <label class="btn btn-xs btn-outline-secondary py-1 px-2 m-0" style="font-size: .75rem; cursor: pointer;" title="Ganti File Template">
                                            <i class="bi bi-pencil-square"></i> Ganti
                                            <input type="file" name="template_file" onchange="this.form.submit()" accept=".doc,.docx,.pdf" style="display: none;">
                                        </label>
                                    </form>
                                </div>
                            <?php else: ?>
                                <form method="POST" action="<?= APP_URL ?>/admin/surat/templateupload/<?= $t['id'] ?>" enctype="multipart/form-data" class="d-flex gap-1 align-items-center m-0">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="file" name="template_file" class="form-control form-control-sm py-1" accept=".doc,.docx,.pdf" required style="max-width: 150px; font-size: .7rem;" onchange="this.form.submit()">
                                    <span class="text-danger small" style="cursor: help" title="Unggah file template (.doc/.docx/.pdf) agar warga dapat mengunduh">* Wajib</span>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button type="button" onclick="deleteTemplate(<?= $t['id'] ?>, '<?= htmlspecialchars($t['nama']) ?>')" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: .75rem" title="Hapus">
                                <i class="bi bi-trash3-fill"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($templates)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i>
                            Belum ada template surat yang ditambahkan.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$footerExtra = <<<JS
<script>
function deleteTemplate(id, name) {
    Swal.fire({
        title: 'Hapus Template?',
        text: 'Apakah Anda yakin ingin menghapus template "' + name + '"? Jika template telah digunakan dalam pengajuan surat warga, template akan dinonaktifkan secara otomatis.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        
        $.post(APP_URL + '/admin/surat/templatedelete/' + id, {
            _csrf_token: CSRF_TOKEN
        }, res => {
            if (res.success || res.message) {
                Swal.fire('Berhasil!', res.message || 'Template berhasil dihapus/dinonaktifkan.', 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus template.', 'error');
            }
        }).fail(() => {
            Swal.fire('Gagal', 'Terjadi kesalahan koneksi server.', 'error');
        });
    });
}
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/admin.php'; ?>
