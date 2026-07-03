<?php $pageTitle = 'Dashboard Warga'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Greeting Banner -->
<div class="rounded-4 p-4 mb-4 text-white position-relative overflow-hidden" data-aos="fade-down"
     style="background:linear-gradient(135deg,#1e4080 0%,#1a5276 100%)">
    <div class="position-absolute end-0 top-0 opacity-10">
        <i class="bi bi-buildings" style="font-size:8rem"></i>
    </div>
    <div class="row align-items-center">
        <div class="col-sm-8">
            <p class="mb-1 opacity-75 small">Selamat datang,</p>
            <h4 class="fw-black mb-1"><?= htmlspecialchars($user['nama'] ?? '') ?></h4>
            <p class="mb-0 opacity-75 small font-monospace">
                NIK: <?= htmlspecialchars($penduduk['nik'] ?? '—') ?>
                &nbsp;|&nbsp;
                <?= date('l, d F Y') ?>
            </p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <span class="badge bg-white bg-opacity-20 border border-white border-opacity-25 px-3 py-2">
                <i class="bi bi-shield-check me-1"></i>Warga Terdaftar
            </span>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <?php
    $cards = [
        ['icon'=>'bi-file-earmark-text','label'=>'Surat Diajukan','value'=>$stats['total_surat'],   'color'=>'primary'],
        ['icon'=>'bi-hourglass-split',  'label'=>'Sedang Diproses','value'=>$stats['surat_proses'], 'color'=>'warning'],
        ['icon'=>'bi-check-circle',     'label'=>'Surat Selesai',  'value'=>$stats['surat_selesai'],'color'=>'success'],
        ['icon'=>'bi-megaphone',        'label'=>'Pengaduan Aktif','value'=>$stats['pengaduan_aktif'],'color'=>'danger'],
    ];
    foreach ($cards as $c):
    ?>
    <div class="col-6 col-lg-3">
        <div class="card p-3 h-100" style="border-top:3px solid var(--bs-<?= $c['color'] ?>)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size:.7rem;text-transform:uppercase;font-weight:700"><?= $c['label'] ?></p>
                    <h3 class="fw-black mb-0 text-<?= $c['color'] ?>"><?= $c['value'] ?></h3>
                </div>
                <div class="bg-<?= $c['color'] ?> bg-opacity-10 rounded-3 p-2">
                    <i class="bi <?= $c['icon'] ?> text-<?= $c['color'] ?> fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4" data-aos="fade-up" data-aos-delay="100">
    <!-- Riwayat Surat -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-file-earmark-text me-2 text-primary"></i>Riwayat Pengajuan Surat</span>
                <a href="<?= APP_URL ?>/warga/surat" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($suratTerbaru)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-earmark-plus fs-2 d-block mb-2"></i>
                    <p class="small">Belum ada pengajuan surat</p>
                    <a href="<?= APP_URL ?>/warga/surat/create" class="btn btn-sm btn-primary">Ajukan Sekarang</a>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($suratTerbaru as $s): ?>
                    <a href="<?= APP_URL ?>/warga/surat/show/<?= $s['id'] ?>"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                            <i class="bi bi-file-earmark-text text-primary"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate" style="font-size:.85rem">
                                <?= htmlspecialchars($s['jenis_nama']) ?>
                            </div>
                            <small class="text-muted font-monospace"><?= htmlspecialchars($s['nomor']) ?>
                            · <?= date('d/m/Y', strtotime($s['created_at'])) ?></small>
                        </div>
                        <?php
                        $statusConfig = [
                            'menunggu'              => ['bg-warning-subtle text-warning border-warning-subtle',   'Menunggu'],
                            'diverifikasi'          => ['bg-info-subtle text-info border-info-subtle',            'Diverifikasi'],
                            'diproses'              => ['bg-purple-subtle text-purple border-purple-subtle',      'Diproses'],
                            'menunggu_persetujuan'  => ['bg-warning-subtle text-warning border-warning-subtle',   'Menunggu Persetujuan'],
                            'disetujui'             => ['bg-success-subtle text-success border-success-subtle',   'Disetujui'],
                            'selesai'               => ['bg-success-subtle text-success border-success-subtle',   'Selesai'],
                            'ditolak'               => ['bg-danger-subtle text-danger border-danger-subtle',      'Ditolak'],
                        ];
                        [$cls, $lbl] = $statusConfig[$s['status']] ?? ['bg-secondary-subtle text-secondary', $s['status']];
                        ?>
                        <span class="badge border <?= $cls ?> badge-status"><?= $lbl ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Layanan Cepat -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <i class="bi bi-grid me-2 text-primary"></i>Layanan Cepat
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php
                    $services = [
                        ['icon'=>'bi-file-earmark-plus','label'=>'Ajukan Surat',   'url'=>'warga/surat/create',    'color'=>'primary'],
                        ['icon'=>'bi-truck',            'label'=>'Tracking Surat', 'url'=>'warga/surat/tracking',  'color'=>'info'],
                        ['icon'=>'bi-megaphone',        'label'=>'Buat Pengaduan', 'url'=>'warga/pengaduan/create','color'=>'warning'],
                        ['icon'=>'bi-robot',            'label'=>'Tanya AI',       'url'=>'warga/chatbot',         'color'=>'success'],
                        ['icon'=>'bi-globe',            'label'=>'Info Desa',      'url'=>'warga/informasi',       'color'=>'secondary'],
                        ['icon'=>'bi-bell',             'label'=>'Notifikasi',     'url'=>'warga/notifikasi',      'color'=>'danger'],
                    ];
                    foreach ($services as $svc):
                    ?>
                    <div class="col-6">
                        <a href="<?= APP_URL ?>/<?= $svc['url'] ?>"
                           class="card text-decoration-none text-center p-3 h-100 border hover-shadow"
                           style="transition:.2s">
                            <div class="bg-<?= $svc['color'] ?> bg-opacity-10 rounded-3 d-inline-flex p-2 mx-auto mb-2">
                                <i class="bi <?= $svc['icon'] ?> text-<?= $svc['color'] ?>"></i>
                            </div>
                            <small class="fw-semibold text-dark" style="font-size:.72rem"><?= $svc['label'] ?></small>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Berita & Pengumuman -->
<div class="card" data-aos="fade-up" data-aos-delay="150">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-newspaper me-2 text-primary"></i>Berita & Pengumuman Terbaru</span>
        <a href="<?= APP_URL ?>/warga/informasi" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($beritaTerbaru as $b): ?>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border">
                    <div class="rounded-top-3" style="height:100px;background:linear-gradient(135deg,#1e4080,#059669)">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <i class="bi bi-newspaper text-white opacity-25" style="font-size:3rem"></i>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <?php $cat=$b['kategori']; $catColor=['berita'=>'primary','pengumuman'=>'warning','agenda'=>'success'][$cat]??'secondary'; ?>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-<?= $catColor ?>-subtle text-<?= $catColor ?> border border-<?= $catColor ?>-subtle" style="font-size:.65rem">
                                <?= ucfirst($cat) ?>
                            </span>
                            <small class="text-muted" style="font-size:.65rem">
                                <?= date('d M Y', strtotime($b['created_at'])) ?>
                            </small>
                        </div>
                        <h6 class="fw-bold mb-1" style="font-size:.82rem;line-height:1.3">
                            <?= htmlspecialchars($b['judul']) ?>
                        </h6>
                        <?php if ($b['excerpt']): ?>
                        <p class="text-muted mb-0" style="font-size:.72rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                            <?= htmlspecialchars($b['excerpt']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($beritaTerbaru)): ?>
            <div class="col-12 text-center text-muted py-4">
                <i class="bi bi-newspaper fs-2 d-block mb-2"></i>
                Belum ada berita terbaru
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
