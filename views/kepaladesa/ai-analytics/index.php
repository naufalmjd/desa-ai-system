<?php $pageTitle = 'AI Analytics & Geografis'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Peta Sebaran -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-map-fill me-2 text-warning"></i>Peta Sebaran Pengaduan Warga (Prioritas YOLOv8)
            </div>
            <div class="card-body p-0">
                <div id="analyticsMap" class="w-100" style="height: 480px; border-radius: 0 0 14px 14px"></div>
            </div>
        </div>
    </div>

    <!-- Statistik AI -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-cpu-fill me-2 text-warning"></i>Ringkasan Analitik AI
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless small mb-0">
                    <tr><td class="text-muted">Total Laporan Berkoordinat</td>
                        <td class="fw-bold fs-6 text-dark"><?= $stats['total'] ?> Laporan</td></tr>
                </table>
                <hr class="my-3">
                
                <div class="d-flex flex-column gap-2">
                    <?php
                    $pList = [
                        ['kritis', 'KRITIS', 'danger', $stats['kritis']],
                        ['tinggi', 'TINGGI', 'warning', $stats['tinggi']],
                        ['sedang', 'SEDANG', 'primary', $stats['sedang']],
                        ['rendah', 'RENDAH', 'success', $stats['rendah']]
                    ];
                    foreach ($pList as $p):
                    ?>
                    <div class="d-flex align-items-center justify-content-between p-2 rounded border-start border-3 border-<?= $p[2] ?> bg-light">
                        <div>
                            <span class="badge bg-<?= $p[2] ?>-subtle text-<?= $p[2] ?> border border-<?= $p[2] ?>-subtle badge-status" style="font-size: .65rem"><?= $p[1] ?></span>
                        </div>
                        <span class="fw-bold"><?= $p[3] ?> Kasus</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card bg-gradient text-white" style="background: linear-gradient(135deg, #0f172a, #1e293b)">
            <div class="card-body p-4">
                <i class="bi bi-robot fs-2 mb-2 d-block text-warning"></i>
                <h6 class="fw-bold mb-1">Integrasi YOLOv8 & Gemini</h6>
                <p class="small text-white-50 mb-0">Sistem melakukan deteksi objek pada foto aduan warga menggunakan model YOLOv8 untuk menentukan klasifikasi prioritas secara otomatis, mencegah penumpukan laporan penting.</p>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$headExtra = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">';
$mapDataJson = json_encode($mapData);
$footerExtra = <<<JS
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const map = L.map('analyticsMap').setView([-6.2088, 106.8456], 13); // Default view
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const mapData = {$mapDataJson};
    const bounds = [];

    const colors = {
        kritis: '#dc2626',
        tinggi: '#d97706',
        sedang: '#2563eb',
        rendah: '#16a34a'
    };

    mapData.forEach(item => {
        const lat = parseFloat(item.latitude);
        const lng = parseFloat(item.longitude);
        if (!isNaN(lat) && !isNaN(lng)) {
            bounds.push([lat, lng]);
            
            const color = colors[item.prioritas] || '#64748b';
            
            // Create custom circle marker
            const marker = L.circleMarker([lat, lng], {
                radius: 8,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            marker.bindPopup(`
                <div class="small">
                    <strong>\${item.nomor}</strong><br>
                    <span class="badge bg-light text-dark border mb-1">\${item.kategori.toUpperCase()}</span><br>
                    <strong>\${item.judul}</strong><br>
                    <span class="text-muted">\${item.lokasi_alamat}</span>
                </div>
            `);
        }
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds);
    }
});
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/kepaladesa.php'; ?>
