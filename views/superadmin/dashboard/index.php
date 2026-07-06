<?php $pageTitle = 'Dashboard Kontrol Sistem'; ?>

<?php ob_start(); ?>

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex gap-2 rounded-3 mb-4 border-0 text-white" style="background: rgba(var(--bs-<?= $flash['type'] ?>-rgb), 0.2); backdrop-filter: blur(10px);" role="alert">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Welcome Panel -->
<div class="card p-4 mb-4 border-0 overflow-hidden position-relative" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(6, 182, 212, 0.05)) !important;" data-aos="fade-up">
    <div class="position-absolute end-0 top-0 translate-middle-y text-primary opacity-10" style="font-size: 15rem; transform: rotate(-15deg); pointer-events: none;">
        <i class="bi bi-cpu-fill"></i>
    </div>
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1">Selamat Datang, Super Admin!</h2>
            <p class="text-muted mb-0">Ini adalah konsol kontrol utama untuk memantau seluruh pengguna, aktivitas sistem, dan konfigurasi dari aplikasi SIAP-Desa.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= APP_URL ?>/superadmin/user" class="btn btn-primary px-4 py-2">
                <i class="bi bi-person-plus-fill me-2"></i>Kelola Pengguna
            </a>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4" data-aos="fade-up" data-aos-delay="50">
    <?php
    $cards = [
        ['title' => 'Total Pengguna', 'value' => $stats['total_users'], 'sub' => 'Akun aktif & terdaftar', 'icon' => 'bi-people-fill', 'color' => '#6366f1', 'bg' => 'rgba(99, 102, 241, 0.1)'],
        ['title' => 'Warga', 'value' => $stats['total_warga'], 'sub' => 'Layanan mandiri warga', 'icon' => 'bi-person-hearts', 'color' => '#06b6d4', 'bg' => 'rgba(6, 182, 212, 0.1)'],
        ['title' => 'Admin & Kades', 'value' => $stats['total_admin'] + $stats['total_kades'], 'sub' => 'Staf & kepala desa', 'icon' => 'bi-person-gear', 'color' => '#fbbf24', 'bg' => 'rgba(251, 191, 36, 0.1)'],
        ['title' => 'Total Log', 'value' => $stats['total_logs'], 'sub' => 'Aktivitas terekam', 'icon' => 'bi-journal-code', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.1)'],
    ];
    foreach ($cards as $c):
    ?>
    <div class="col-6 col-xl-3">
        <div class="card p-3 h-100 border-0" style="border-left: 4px solid <?= $c['color'] ?> !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: .75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;"><?= $c['title'] ?></p>
                    <h3 class="fw-bold mb-0 text-white"><?= number_format($c['value']) ?></h3>
                    <small class="text-muted" style="font-size: 0.75rem;"><?= $c['sub'] ?></small>
                </div>
                <div class="rounded-3 p-2" style="background: <?= $c['bg'] ?>">
                    <i class="bi <?= $c['icon'] ?> fs-4" style="color: <?= $c['color'] ?>"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4" data-aos="fade-up" data-aos-delay="100">
    <!-- Log Aktivitas Terbaru -->
    <div class="col-lg-8">
        <div class="card h-100 border-0">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <span class="fs-6 fw-bold"><i class="bi bi-clock-history me-2 text-accent"></i>Aktivitas Sistem Terbaru</span>
                <a href="<?= APP_URL ?>/superadmin/log" class="btn btn-sm btn-outline-light" style="border-color: rgba(255,255,255,0.05); font-size: 0.75rem;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>Modul</th>
                                <th>Deskripsi</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLogs as $log): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary fw-bold" style="width: 28px; height: 28px; font-size: 0.7rem; color: #fff !important;">
                                            <?= strtoupper(substr($log['username'] ?? 'S', 0, 2)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-semibold text-white d-block"><?= htmlspecialchars($log['username'] ?? 'System') ?></span>
                                            <small class="text-muted" style="font-size: 0.7rem;"><?= $log['ip_address'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge text-uppercase" style="background: rgba(255,255,255,0.05); font-size: 0.65rem; border: 1px solid rgba(255,255,255,0.05);">
                                        <?= htmlspecialchars($log['module']) ?>
                                    </span>
                                </td>
                                <td class="text-wrap" style="max-width: 250px;">
                                    <span style="font-size: 0.82rem; color: var(--text-muted);"><?= htmlspecialchars($log['description']) ?></span>
                                </td>
                                <td>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y, H:i', strtotime($log['created_at'])) ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentLogs)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada aktivitas terekam.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Control Panel -->
    <div class="col-lg-4">
        <div class="card h-100 border-0">
            <div class="card-header bg-transparent py-3">
                <span class="fs-6 fw-bold"><i class="bi bi-sliders me-2 text-warning"></i>Kontrol Cepat</span>
            </div>
            <div class="card-body d-flex flex-column gap-3">
                <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255,255,255,0.03);">
                    <h6 class="fw-semibold text-white mb-2" style="font-size: 0.85rem;"><i class="bi bi-server me-2 text-accent"></i>Informasi Host Database</h6>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.78rem;">
                        <span class="text-muted">Driver:</span>
                        <code class="text-gradient">MySQL (MariaDB)</code>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.78rem;">
                        <span class="text-muted">Database:</span>
                        <code class="text-gradient">desa_ai_system</code>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size: 0.78rem;">
                        <span class="text-muted">Status:</span>
                        <span class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Connected</span>
                    </div>
                </div>

                <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255,255,255,0.03);">
                    <h6 class="fw-semibold text-white mb-2" style="font-size: 0.85rem;"><i class="bi bi-robot me-2 text-primary"></i>Server AI Integrasi</h6>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.78rem;">
                        <span class="text-muted">API URL:</span>
                        <code class="text-gradient"><?= defined('AI_SERVER_URL') ? AI_SERVER_URL : 'Unknown' ?></code>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size: 0.78rem;">
                        <span class="text-muted">Timeout:</span>
                        <code class="text-gradient"><?= defined('AI_TIMEOUT') ? AI_TIMEOUT . ' detik' : 'Unknown' ?></code>
                    </div>
                </div>

                <div class="mt-auto d-grid gap-2">
                    <a href="<?= APP_URL ?>/superadmin/user/create" class="btn btn-primary btn-sm py-2">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Akun Staf/Warga
                    </a>
                    <a href="<?= APP_URL ?>/superadmin/log" class="btn btn-outline-light btn-sm py-2" style="border-color: rgba(255,255,255,0.1)">
                        <i class="bi bi-database-check me-1"></i> Audit Log Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require VIEW_PATH . '/layouts/superadmin.php'; ?>
