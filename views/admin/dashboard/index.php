<?php $pageTitle = 'Dashboard Admin'; ?>

<?php ob_start(); ?>

<!-- Flash -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- KPI Cards -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <?php
    $kpis = [
        ['icon'=>'bi-people-fill',       'label'=>'Total Penduduk',    'value'=>number_format($stats['total_penduduk']),
         'sub'=>'warga terdaftar',        'color'=>'#1e4080', 'bg'=>'#e8edf5'],
        ['icon'=>'bi-file-earmark-text', 'label'=>'Surat Menunggu',    'value'=>$stats['surat_menunggu'],
         'sub'=>'perlu verifikasi',       'color'=>'#d97706', 'bg'=>'#fef3c7'],
        ['icon'=>'bi-megaphone-fill',    'label'=>'Pengaduan Aktif',   'value'=>$stats['pengaduan_aktif'],
         'sub'=>'belum selesai',          'color'=>'#dc2626', 'bg'=>'#fee2e2'],
        ['icon'=>'bi-graph-up-arrow',    'label'=>'Penyelesaian',      'value'=>$stats['penyelesaian_pct'].'%',
         'sub'=>'tingkat penyelesaian',   'color'=>'#059669', 'bg'=>'#d1fae5'],
    ];
    foreach ($kpis as $k):
    ?>
    <div class="col-6 col-xl-3">
        <div class="card p-3 h-100" style="border-left:4px solid <?= $k['color'] ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;font-weight:700"><?= $k['label'] ?></p>
                    <h3 class="fw-black mb-0" style="color:<?= $k['color'] ?>"><?= $k['value'] ?></h3>
                    <small class="text-muted"><?= $k['sub'] ?></small>
                </div>
                <div class="rounded-3 p-2" style="background:<?= $k['bg'] ?>">
                    <i class="bi <?= $k['icon'] ?> fs-4" style="color:<?= $k['color'] ?>"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Charts -->
<div class="row g-3 mb-4" data-aos="fade-up" data-aos-delay="100">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Tren Pengajuan Surat (<?= date('Y') ?>)</span>
                <span class="badge bg-primary-subtle text-primary">Monthly</span>
            </div>
            <div class="card-body">
                <canvas id="suratChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <span><i class="bi bi-pie-chart-fill me-2 text-danger"></i>Kategori Pengaduan</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center">
                <canvas id="aduanPieChart" width="200" height="200"></canvas>
                <div class="w-100 mt-3" id="pieLabels"></div>
            </div>
        </div>
    </div>
</div>

<!-- Pengajuan Terbaru -->
<div class="card" data-aos="fade-up" data-aos-delay="150">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-file-earmark-text me-2 text-warning"></i>Pengajuan Surat — Perlu Aksi</span>
        <a href="<?= APP_URL ?>/admin/surat?status=menunggu" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="suratTable">
                <thead>
                    <tr>
                        <th>No. Surat</th><th>Jenis</th><th>Pemohon</th>
                        <th>NIK</th><th>Tanggal</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($suratPending as $s): ?>
                <tr>
                    <td><code class="text-primary"><?= htmlspecialchars($s['nomor']) ?></code></td>
                    <td><?= htmlspecialchars($s['jenis_nama']) ?></td>
                    <td><?= htmlspecialchars($s['pemohon_nama']) ?></td>
                    <td><code><?= htmlspecialchars($s['nik']) ?></code></td>
                    <td><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                    <td>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle badge-status">
                            Menunggu
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= APP_URL ?>/admin/surat/show/<?= $s['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-success"
                                    onclick="confirmAction('<?= APP_URL ?>/admin/surat/verifikasi/<?= $s['id'] ?>', 'Verifikasi Surat?', 'Surat akan diverifikasi dan diteruskan.')"
                                    title="Verifikasi">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="tolakSurat(<?= $s['id'] ?>)"
                                    title="Tolak">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($suratPending)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">
                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                    Semua pengajuan sudah ditangani
                </td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$suratMonthlyJson = json_encode($suratMonthly);
$pengaduanStatsJson = json_encode($pengaduanStats);
$footerExtra = <<<JS
<script>
// ── Surat Chart ────────────────────────────────────────────────────────────────
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const monthlyRaw = {$suratMonthlyJson};
const totals  = Array(12).fill(0);
const selesais = Array(12).fill(0);
monthlyRaw.forEach(r => { totals[r.bulan-1]=r.total; selesais[r.bulan-1]=r.selesai; });

new Chart(document.getElementById('suratChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            { label:'Total Pengajuan', data:totals,   backgroundColor:'rgba(30,64,128,.7)',  borderRadius:4 },
            { label:'Selesai',         data:selesais,  backgroundColor:'rgba(5,150,105,.65)', borderRadius:4 },
        ]
    },
    options: { responsive:true, plugins:{ legend:{ position:'top',labels:{boxWidth:12,font:{size:11}}}},
               scales:{ x:{ticks:{font:{size:11}}}, y:{ticks:{font:{size:11},stepSize:5}} } }
});

// ── Pie Chart ─────────────────────────────────────────────────────────────────
const kategoriRaw = {$pengaduanStatsJson};
const COLORS = ['#dc2626','#d97706','#2563eb','#7c3aed','#059669','#0891b2'];
const pieLabels = kategoriRaw.map(r => r.kategori.replace('_',' ').toUpperCase());
const pieData   = kategoriRaw.map(r => r.total);

new Chart(document.getElementById('aduanPieChart'), {
    type: 'doughnut',
    data: {
        labels: pieLabels,
        datasets:[{ data:pieData, backgroundColor:COLORS, borderWidth:2, hoverOffset:4 }]
    },
    options: { plugins:{ legend:{ display:false }}, cutout:'65%' }
});

const labelsEl = document.getElementById('pieLabels');
kategoriRaw.forEach((r,i) => {
    labelsEl.innerHTML += `<div class="d-flex justify-content-between align-items-center mb-1" style="font-size:.75rem">
        <span><span class="d-inline-block rounded me-1" style="width:8px;height:8px;background:\${COLORS[i]}"></span>\${pieLabels[i]}</span>
        <strong>\${pieData[i]}</strong></div>`;
});

// ── Tolak Surat Modal ─────────────────────────────────────────────────────────
function tolakSurat(id) {
    Swal.fire({
        title: 'Tolak Pengajuan?',
        input: 'textarea',
        inputPlaceholder: 'Masukkan alasan penolakan...',
        inputAttributes: { required: true },
        showCancelButton: true,
        confirmButtonText: 'Tolak',
        confirmButtonColor: '#dc2626',
        cancelButtonText: 'Batal',
        preConfirm: alasan => {
            if (!alasan) { Swal.showValidationMessage('Alasan wajib diisi!'); return false; }
            return fetch(APP_URL + '/admin/surat/tolak/' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: '_csrf_token=' + CSRF_TOKEN + '&alasan=' + encodeURIComponent(alasan)
            }).then(r => r.json());
        }
    }).then(result => {
        if (result.value?.success) {
            Swal.fire('Ditolak!', 'Pengajuan berhasil ditolak.', 'success').then(() => location.reload());
        }
    });
}
</script>
JS;
?>
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
