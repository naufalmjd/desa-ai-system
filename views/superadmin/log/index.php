<?php $pageTitle = 'Audit Log Aktivitas'; ?>

<?php ob_start(); ?>

<!-- Search and Filter Form -->
<div class="card p-3 mb-4 border-0" data-aos="fade-up">
    <form action="" method="GET" class="row g-3 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-secondary text-muted" style="border-right: none !important;"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Cari deskripsi log, IP, atau nama pengguna..." value="<?= htmlspecialchars($search) ?>" style="border-left: none !important;">
            </div>
        </div>
        <div class="col-md-4">
            <select name="module" class="form-select">
                <option value="">Semua Modul</option>
                <?php foreach ($modules as $m): ?>
                <option value="<?= htmlspecialchars($m['module']) ?>" <?= $module === $m['module'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars(strtoupper($m['module'])) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary py-2">
                <i class="bi bi-funnel-fill me-1"></i> Filter Log
            </button>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="card border-0 overflow-hidden" data-aos="fade-up" data-aos-delay="50">
    <div class="card-header bg-transparent py-3 border-0">
        <span class="fs-6 fw-bold"><i class="bi bi-journal-text me-2 text-warning"></i>Daftar Riwayat Aktivitas</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Pengguna</th>
                        <th>Tindakan (Action)</th>
                        <th>Modul</th>
                        <th>Deskripsi</th>
                        <th>Alamat IP</th>
                        <th>Waktu Kejadian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = ($result['current_page'] - 1) * $result['per_page'] + 1;
                    foreach ($result['data'] as $log): 
                    ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $no++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary fw-bold" style="width: 28px; height: 28px; font-size: 0.7rem; color: #fff !important;">
                                    <?= strtoupper(substr($log['username'] ?? 'SY', 0, 2)) ?>
                                </div>
                                <span class="fw-semibold text-white"><?= htmlspecialchars($log['username'] ?? 'System') ?></span>
                            </div>
                        </td>
                        <td>
                            <code style="color: var(--accent); font-size: 0.8rem;"><?= htmlspecialchars($log['action']) ?></code>
                        </td>
                        <td>
                            <span class="badge text-uppercase" style="background: rgba(255,255,255,0.04); font-size: 0.68rem; border: 1px solid rgba(255,255,255,0.05); color: var(--text-muted);">
                                <?= htmlspecialchars($log['module']) ?>
                            </span>
                        </td>
                        <td class="text-wrap" style="max-width: 320px;">
                            <span style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($log['description']) ?></span>
                        </td>
                        <td>
                            <code class="text-gradient" style="font-size: 0.8rem;"><?= htmlspecialchars($log['ip_address'] ?: '-') ?></code>
                        </td>
                        <td>
                            <div style="font-size: 0.78rem;">
                                <span class="text-white d-block"><?= date('d/m/Y', strtotime($log['created_at'])) ?></span>
                                <small class="text-muted"><?= date('H:i:s', strtotime($log['created_at'])) ?></small>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($result['data'])): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada aktivitas terekam.
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
            Menampilkan <?= count($result['data']) ?> dari <?= number_format($result['total']) ?> log aktivitas
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $result['last_page']; $i++): ?>
                <li class="page-item <?= $i === $result['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&module=<?= urlencode($module) ?>" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.05); color: var(--text-muted);">
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
    // Style active state of custom pagination links
    $('.pagination .active a').css({
        'background': 'linear-gradient(135deg, var(--primary), var(--primary-dark))',
        'border-color': 'var(--primary)',
        'color': '#fff'
    });
});
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/superadmin.php'; ?>
