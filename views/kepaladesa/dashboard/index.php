<?php $pageTitle = 'Dashboard Eksekutif'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- KPI Overview -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <?php
    $cards = [
        ['icon' => 'bi-people-fill', 'label' => 'Total Penduduk', 'value' => number_format($stats['total_penduduk']), 'sub' => 'Warga terdaftar', 'color' => '#0f172a', 'bg' => '#f1f5f9'],
        ['icon' => 'bi-file-earmark-check-fill', 'label' => 'Persetujuan Surat', 'value' => $stats['surat_ttd'], 'sub' => 'Menunggu TTD Anda', 'color' => '#d97706', 'bg' => '#fef3c7'],
        ['icon' => 'bi-exclamation-triangle-fill', 'label' => 'Aduan Kritis', 'value' => $stats['pengaduan_kritis'], 'sub' => 'Perlu tindakan cepat', 'color' => '#dc2626', 'bg' => '#fee2e2'],
        ['icon' => 'bi-check-circle-fill', 'label' => 'Penyelesaian Aduan', 'value' => $stats['penyelesaian_aduan'] . '%', 'sub' => 'Tingkat solusi aduan', 'color' => '#059669', 'bg' => '#d1fae5']
    ];
    foreach ($cards as $c):
    ?>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 h-100" style="border-left: 4px solid <?= $c['color'] ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size:.7rem;text-transform:uppercase;font-weight:700"><?= $c['label'] ?></p>
                    <h3 class="fw-black mb-0" style="color:<?= $c['color'] ?>"><?= $c['value'] ?></h3>
                    <small class="text-muted" style="font-size:.7rem"><?= $c['sub'] ?></small>
                </div>
                <div class="rounded-3 p-2" style="background:<?= $c['bg'] ?>">
                    <i class="bi <?= $c['icon'] ?> fs-4" style="color:<?= $c['color'] ?>"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4" data-aos="fade-up" data-aos-delay="100">
    <!-- Trend & Chart -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-graph-up me-2 text-warning"></i>Tren Administrasi & Pelayanan (<?= date('Y') ?>)</span>
                <span class="badge bg-warning-subtle text-warning">Bulanan</span>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="110"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Radar Performance -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <span><i class="bi bi-compass me-2 text-primary"></i>Kepuasan & Sektor Pelayanan</span>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="kpiRadarChart" width="220" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4" data-aos="fade-up" data-aos-delay="150">
    <!-- Surat Menunggu TTD -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-file-earmark-text-fill me-2 text-warning"></i>Daftar Tunggu Persetujuan Surat</span>
                <a href="<?= APP_URL ?>/kepaladesa/surat" class="btn btn-sm btn-outline-warning">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No. Surat</th><th>Jenis</th><th>Pemohon</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suratMenunggu as $s): ?>
                            <tr>
                                <td><code class="text-primary"><?= htmlspecialchars($s['nomor']) ?></code></td>
                                <td><?= htmlspecialchars($s['jenis_nama']) ?></td>
                                <td class="fw-medium"><?= htmlspecialchars($s['pemohon_nama']) ?></td>
                                <td>
                                    <a href="<?= APP_URL ?>/kepaladesa/surat/show/<?= $s['id'] ?>" class="btn btn-xs btn-warning py-1 px-2 text-white" style="font-size:.7rem">
                                        Tinjau TTD
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($suratMenunggu)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-check-circle-fill text-success fs-3 d-block mb-1"></i>Belum ada surat perlu TTD</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Aduan Kritis / Tinggi -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Aduan Prioritas Tinggi (YOLOv8)</span>
                <a href="<?= APP_URL ?>/kepaladesa/ai-analytics" class="btn btn-sm btn-outline-danger">Peta Distribusi</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No. Aduan</th><th>Judul</th><th>Tingkat</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengaduanKritis as $p): ?>
                            <tr>
                                <td><code class="text-danger"><?= htmlspecialchars($p['nomor']) ?></code></td>
                                <td class="fw-semibold text-truncate" style="max-width:180px"><?= htmlspecialchars($p['judul']) ?></td>
                                <td>
                                    <?php $c = $p['prioritas'] === 'kritis' ? 'danger' : 'warning'; ?>
                                    <span class="badge bg-<?= $c ?>-subtle text-<?= $c ?> border border-<?= $c ?>-subtle badge-status">
                                        <?= strtoupper($p['prioritas']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light border text-muted badge-status"><?= ucfirst($p['status']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pengaduanKritis)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-shield-fill-check text-success fs-3 d-block mb-1"></i>Aman! Tidak ada aduan kritis</td></tr>
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
$aduanMonthlyJson = json_encode($aduanMonthly);
$suratMonthlyJson = json_encode($suratMonthly);
$kpiDataJson = json_encode($kpiData);
$footerExtra = <<<JS
<script>
// ── Trend Line & Bar Chart ──────────────────────────────────────────────────
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const aduanRaw = {$aduanMonthlyJson};
const suratRaw = {$suratMonthlyJson};

const aduanData = Array(12).fill(0);
const suratData = Array(12).fill(0);

aduanRaw.forEach(r => { aduanData[r.bulan - 1] = r.total; });
suratRaw.forEach(r => { suratData[r.bulan - 1] = r.total; });

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [
            { label: 'Pengaduan Masuk', data: aduanData, borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.05)', fill: true, tension: 0.35, borderWidth: 2.5 },
            { label: 'Surat Diproses', data: suratData, borderColor: '#d97706', backgroundColor: 'rgba(217,119,6,0.05)', fill: true, tension: 0.35, borderWidth: 2.5 }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { font: { size: 11 } } } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 5 } } }
    }
});

// ── KPI Radar Chart ──────────────────────────────────────────────────────────
const radarData = {$kpiDataJson};
const radarLabels = radarData.map(r => r.subject);
const radarValues = radarData.map(r => r.value);

new Chart(document.getElementById('kpiRadarChart'), {
    type: 'radar',
    data: {
        labels: radarLabels,
        datasets: [{
            label: 'Efisiensi Sektor',
            data: radarValues,
            backgroundColor: 'rgba(217,119,6,0.2)',
            borderColor: '#d97706',
            pointBackgroundColor: '#b45309',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { r: { angleLines: { display: true }, suggestMin: 50, suggestMax: 100 } }
    }
});
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/kepaladesa.php'; ?>
