<?php $pageTitle = 'Laporan Pelayanan Desa'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Ringkasan Laporan -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i>Ringkasan Volume Pelayanan
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 text-center border-end p-3">
                        <small class="text-muted d-block mb-1">Pengajuan Surat Masuk</small>
                        <h2 class="fw-black text-primary mb-1"><?= $stats['total_surat'] ?></h2>
                        <span class="text-muted small">Tingkat selesai: <?= $stats['total_surat'] > 0 ? round(($stats['surat_selesai'] / $stats['total_surat']) * 100, 1) : 0 ?>%</span>
                    </div>
                    <div class="col-sm-6 text-center p-3">
                        <small class="text-muted d-block mb-1">Aduan Warga Masuk</small>
                        <h2 class="fw-black text-danger mb-1"><?= $stats['total_pengaduan'] ?></h2>
                        <span class="text-muted small">Tingkat selesai: <?= $stats['total_pengaduan'] > 0 ? round(($stats['pengaduan_selesai'] / $stats['total_pengaduan']) * 100, 1) : 0 ?>%</span>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary py-2" onclick="Swal.fire('Fitur Cetak', 'Mengunduh laporan PDF bulanan...', 'success')" disabled>
                        <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Laporan PDF Bulanan
                    </button>
                    <button class="btn btn-outline-success py-2" onclick="Swal.fire('Fitur Ekspor', 'Mengunduh laporan Excel...', 'success')" disabled>
                        <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Laporan ke Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi/Estimasi Pelayanan -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <i class="bi bi-info-circle-fill me-2 text-warning"></i>Statistik Pelayanan Warga
            </div>
            <div class="card-body small">
                <p class="text-muted mb-4">Laporan kinerja pelayanan administrasi dan aduan masyarakat RT/RW Desa Sukamaju.</p>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <span class="text-muted">Surat TTD Selesai:</span>
                        <strong class="text-dark"><?= $stats['surat_selesai'] ?> / <?= $stats['total_surat'] ?></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <span class="text-muted">Pengaduan Selesai ditangani:</span>
                        <strong class="text-dark"><?= $stats['pengaduan_selesai'] ?> / <?= $stats['total_pengaduan'] ?></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2 border-0">
                        <span class="text-muted">Rata-rata Waktu Proses Surat:</span>
                        <strong class="text-dark">1-2 Hari Kerja</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
