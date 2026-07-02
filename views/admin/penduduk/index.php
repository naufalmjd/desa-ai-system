<?php $pageTitle = 'Kelola Penduduk'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <div class="col-md-4">
        <div class="card p-3 border-primary border-start border-4 border-top-0 border-bottom-0 border-end-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0" style="font-size:.7rem;text-transform:uppercase;font-weight:700">Total Penduduk</p>
                    <h3 class="fw-black text-primary mb-0"><?= number_format($stats['total']) ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 rounded-3 p-2">
                    <i class="bi bi-people-fill text-primary fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Penduduk -->
<div class="card" data-aos="fade-up" data-aos-delay="100">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 flex-wrap gap-2">
        <span><i class="bi bi-table me-2 text-primary"></i>Data Penduduk</span>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" value="<?= htmlspecialchars($filters['search']) ?>"
                       class="form-control form-control-sm" placeholder="Cari nama / NIK..."
                       style="width:200px">
                <select name="jk" class="form-select form-select-sm" style="width:120px">
                    <option value="">Semua JK</option>
                    <option value="L" <?= $filters['jk']==='L'?'selected':'' ?>>Laki-laki</option>
                    <option value="P" <?= $filters['jk']==='P'?'selected':'' ?>>Perempuan</option>
                </select>
                <select name="status" class="form-select form-select-sm" style="width:140px">
                    <option value="">Semua Status</option>
                    <?php foreach(['Tetap','Sementara','Pindah','Meninggal'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filters['status']===$s?'selected':'' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
            </form>
            <a href="<?= APP_URL ?>/admin/penduduk/create" class="btn btn-sm btn-success">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </a>
            <a href="<?= APP_URL ?>/admin/penduduk/export" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-download me-1"></i>Export
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="pendudukTable">
                <thead>
                    <tr>
                        <th>NIK</th><th>Nama Lengkap</th><th>Alamat</th>
                        <th>TTL</th><th>JK</th><th>Agama</th>
                        <th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($result['data'] as $p): ?>
                <tr>
                    <td><code class="text-primary" style="font-size:.72rem"><?= htmlspecialchars($p['nik']) ?></code></td>
                    <td class="fw-medium"><?= htmlspecialchars($p['nama']) ?></td>
                    <td class="text-muted" style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        <?= htmlspecialchars($p['alamat']) ?>
                    </td>
                    <td class="text-muted" style="font-size:.75rem">
                        <?= htmlspecialchars($p['tempat_lahir']) ?>,
                        <?= date('d/m/Y', strtotime($p['tanggal_lahir'])) ?>
                    </td>
                    <td><?= $p['jenis_kelamin'] === 'L' ? '<i class="bi bi-gender-male text-primary"></i>' : '<i class="bi bi-gender-female text-danger"></i>' ?></td>
                    <td class="text-muted" style="font-size:.78rem"><?= htmlspecialchars($p['agama']) ?></td>
                    <td>
                        <?php
                        $sc = match($p['status_penduduk']) {
                            'Tetap'     => 'success',
                            'Sementara' => 'warning',
                            'Pindah'    => 'secondary',
                            'Meninggal' => 'dark',
                            default     => 'secondary',
                        };
                        ?>
                        <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?> border border-<?= $sc ?>-subtle badge-status">
                            <?= htmlspecialchars($p['status_penduduk']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= APP_URL ?>/admin/penduduk/show/<?= $p['id'] ?>"
                               class="btn btn-xs btn-outline-primary" title="Detail"
                               style="padding:2px 6px;font-size:.7rem">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= APP_URL ?>/admin/penduduk/edit/<?= $p['id'] ?>"
                               class="btn btn-xs btn-outline-warning" title="Edit"
                               style="padding:2px 6px;font-size:.7rem">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="confirmAction('<?= APP_URL ?>/admin/penduduk/delete/<?= $p['id'] ?>','Hapus Data?','Data penduduk ini akan dihapus.')"
                               class="btn btn-xs btn-outline-danger" title="Hapus"
                               style="padding:2px 6px;font-size:.7rem">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($result['data'])): ?>
                <tr><td colspan="8" class="text-center text-muted py-5">
                    <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                    Data tidak ditemukan
                </td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($result['last_page'] > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">
            Menampilkan <?= count($result['data']) ?> dari <?= number_format($result['total']) ?> data
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $result['last_page']; $i++): ?>
                <li class="page-item <?= $i===$result['current_page']?'active':'' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($filters['search']) ?>">
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
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
