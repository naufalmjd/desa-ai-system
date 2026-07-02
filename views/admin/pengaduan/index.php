<?php $pageTitle = 'Kelola Pengaduan'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <?php
    $statusMap = [
        'menunggu' => ['label' => 'Menunggu Tindakan', 'color' => '#d97706', 'bg' => '#fef3c7', 'icon' => 'bi-hourglass-split'],
        'ditindaklanjuti' => ['label' => 'Ditindaklanjuti', 'color' => '#0284c7', 'bg' => '#e0f2fe', 'icon' => 'bi-arrow-right-circle'],
        'diproses' => ['label' => 'Sedang Diproses', 'color' => '#7c3aed', 'bg' => '#f3e8ff', 'icon' => 'bi-gear'],
        'selesai' => ['label' => 'Selesai Ditangani', 'color' => '#059669', 'bg' => '#d1fae5', 'icon' => 'bi-check-circle']
    ];
    foreach ($statusMap as $status => $cfg):
        $val = 0;
        foreach ($stats as $s) {
            if ($s['status'] === $status) $val = $s['total'];
        }
    ?>
    <div class="col-6 col-md-3">
        <div class="card p-3 h-100" style="border-left: 4px solid <?= $cfg['color'] ?>">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0" style="font-size: .7rem; text-transform: uppercase; font-weight: 700"><?= $cfg['label'] ?></p>
                    <h3 class="fw-black mb-0" style="color: <?= $cfg['color'] ?>"><?= $val ?></h3>
                </div>
                <div class="rounded-3 p-2" style="background: <?= $cfg['bg'] ?>">
                    <i class="bi <?= $cfg['icon'] ?> fs-5" style="color: <?= $cfg['color'] ?>"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card" data-aos="fade-up" data-aos-delay="100">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 flex-wrap gap-2">
        <span><i class="bi bi-megaphone-fill me-2 text-warning"></i>Daftar Laporan Pengaduan Warga</span>
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="q" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="form-control form-control-sm" placeholder="Cari nomor / judul..." style="width: 170px">
            <select name="status" class="form-select form-select-sm" style="width: 130px">
                <option value="">Semua Status</option>
                <?php foreach ([
                    'menunggu' => 'Menunggu',
                    'ditindaklanjuti' => 'Ditindaklanjuti',
                    'diproses' => 'Diproses',
                    'selesai' => 'Selesai',
                    'ditutup' => 'Ditutup'
                ] as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= ($filters['status'] ?? '')===$val?'selected':'' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
            <select name="prioritas" class="form-select form-select-sm" style="width: 120px">
                <option value="">Prioritas AI</option>
                <?php foreach (['kritis' => 'Kritis', 'tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= ($filters['prioritas'] ?? '')===$val?'selected':'' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Aduan</th><th>Judul Laporan</th><th>Kategori</th><th>Pelapor</th><th>Tanggal</th><th>Prioritas AI</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $p): ?>
                    <tr>
                        <td><code class="text-danger"><?= htmlspecialchars($p['nomor']) ?></code></td>
                        <td class="fw-semibold text-truncate" style="max-width: 160px"><?= htmlspecialchars($p['judul']) ?></td>
                        <td>
                            <span class="badge bg-light border text-dark">
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $p['kategori']))) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($p['pelapor_nama']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                        <td>
                            <?php
                            $pColors = ['kritis' => 'danger', 'tinggi' => 'warning', 'sedang' => 'primary', 'rendah' => 'success'];
                            $pri = $p['prioritas_ai'] ?? 'sedang';
                            $c = $pColors[$pri];
                            ?>
                            <span class="badge bg-<?= $c ?>-subtle text-<?= $c ?> border border-<?= $c ?>-subtle badge-status">
                                <?= strtoupper($pri) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusConfig = [
                                'menunggu'          => ['bg-warning-subtle text-warning border-warning-subtle', 'Menunggu'],
                                'ditindaklanjuti'   => ['bg-info-subtle text-info border-info-subtle',          'Ditindaklanjuti'],
                                'diproses'          => ['bg-purple-subtle text-purple border-purple-subtle',      'Diproses'],
                                'selesai'           => ['bg-success-subtle text-success border-success-subtle',   'Selesai'],
                                'ditutup'           => ['bg-secondary-subtle text-secondary border-secondary-subtle', 'Ditutup']
                            ];
                            [$cls, $lbl] = $statusConfig[$p['status']] ?? ['bg-secondary-subtle text-secondary', $p['status']];
                            ?>
                            <span class="badge border <?= $cls ?> badge-status"><?= $lbl ?></span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/admin/pengaduan/show/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: .75rem">
                                <i class="bi bi-pencil-square"></i> Tanggapi
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($result['data'])): ?>
                    <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-megaphone fs-2 d-block mb-1"></i>Belum ada laporan pengaduan masuk</td></tr>
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
                    <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($filters['search'] ?? '') ?>&status=<?= urlencode($filters['status'] ?? '') ?>">
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
