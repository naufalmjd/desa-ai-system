<?php $pageTitle = 'Tanggapi Pengaduan'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Detail Pengaduan -->
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-megaphone-fill me-2 text-warning"></i>Rincian Pengaduan Warga
            </div>
            <div class="card-body">
                <table class="table table-borderless align-middle" style="font-size: .85rem">
                    <tr><td class="text-muted" style="width: 150px">No. Laporan</td>
                        <td><code class="text-danger font-monospace fs-6"><?= htmlspecialchars($pengaduan['nomor']) ?></code></td></tr>
                    <tr><td class="text-muted">Judul Laporan</td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($pengaduan['judul']) ?></td></tr>
                    <tr><td class="text-muted">Pelapor</td>
                        <td class="fw-semibold"><?= htmlspecialchars($pengaduan['pelapor_nama']) ?> (NIK: <?= htmlspecialchars($pengaduan['pelapor_nik']) ?>)</td></tr>
                    <tr><td class="text-muted">Kategori</td>
                        <td><span class="badge bg-light border text-dark"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $pengaduan['kategori']))) ?></span></td></tr>
                    <tr><td class="text-muted">Lokasi Kejadian</td>
                        <td><?= htmlspecialchars($pengaduan['lokasi_alamat']) ?></td></tr>
                    <tr><td class="text-muted">Koordinat GPS</td>
                        <td><code><?= $pengaduan['latitude'] ? htmlspecialchars($pengaduan['latitude'] . ', ' . $pengaduan['longitude']) : 'Tidak terdeteksi' ?></code></td></tr>
                    <tr><td class="text-muted">Deskripsi Laporan</td>
                        <td><?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?></td></tr>
                    <tr><td class="text-muted">Tanggal Masuk</td>
                        <td><?= date('d F Y H:i', strtotime($pengaduan['created_at'])) ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Form Tanggapan -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-chat-left-text-fill me-2 text-primary"></i>Tindak Lanjut & Tanggapan Admin
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/admin/pengaduan/tanggapan/<?= $pengaduan['id'] ?>">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Status Penanganan <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <?php foreach ([
                                'menunggu'          => 'Menunggu Tindakan',
                                'ditindaklanjuti'   => 'Ditindaklanjuti',
                                'diproses'          => 'Sedang Diproses',
                                'selesai'           => 'Selesai Ditangani',
                                'ditutup'           => 'Tutup Kasus'
                            ] as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= $pengaduan['status']===$val?'selected':'' ?>><?= $lbl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Respon / Tanggapan Resmi</label>
                        <textarea name="tanggapan" class="form-control" rows="4" placeholder="Tuliskan tindakan atau jawaban resmi dari kelurahan terkait masalah ini..." required><?= htmlspecialchars($pengaduan['tanggapan_admin'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary fw-bold w-100 py-2">
                        <i class="bi bi-save-fill me-1"></i> Simpan Tanggapan Laporan
                    </button>
                </form>
            </div>
        </div>

        <!-- Peta Lokasi -->
        <?php if ($pengaduan['latitude'] && $pengaduan['longitude']): ?>
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>Lokasi Geografis Peta
            </div>
            <div class="card-body p-0">
                <div id="complaintMap" class="w-100" style="height: 250px; border-radius: 0 0 14px 14px"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Media Bukti & YOLOv8 AI -->
    <div class="col-lg-5">
        <!-- Foto Bukti -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-camera-fill me-2 text-primary"></i>Foto Bukti Kerusakan/Masalah
            </div>
            <div class="card-body p-3 text-center">
                <?php if (!empty($media)): ?>
                <img src="<?= APP_URL ?><?= htmlspecialchars($media[0]['path']) ?>" class="img-fluid rounded-3 border" style="max-height: 280px; width: 100%; object-fit: cover">
                <?php else: ?>
                <div class="text-center py-5 text-muted small"><i class="bi bi-image fs-1 d-block mb-1"></i>Tidak ada foto terlampir</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- AI Panel -->
        <?php if ($hasilAi): ?>
        <div class="card">
            <div class="card-header bg-white py-3 d-flex align-items-center gap-2">
                <i class="bi bi-cpu-fill text-success"></i>
                <span>Hasil Klasifikasi AI YOLOv8</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle ms-auto" style="font-size: .68rem">YOLOv8</span>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless small mb-0">
                    <tr><td class="text-muted">Deteksi Objek</td>
                        <td class="fw-bold"><?= htmlspecialchars($hasilAi['kategori_deteksi']) ?></td></tr>
                    <tr><td class="text-muted">Skor Kepercayaan</td>
                        <td class="fw-bold text-success"><?= htmlspecialchars($hasilAi['confidence_score']) ?>%</td></tr>
                    <tr><td class="text-muted">Prioritas Rekomendasi</td>
                        <td>
                            <?php $pColors = ['kritis'=>'danger','tinggi'=>'warning','sedang'=>'primary','rendah'=>'success'];
                            $pVal = $hasilAi['prioritas_ai']; ?>
                            <span class="badge bg-<?= $pColors[$pVal] ?>-subtle text-<?= $pColors[$pVal] ?> border border-<?= $pColors[$pVal] ?>-subtle badge-status"><?= strtoupper($pVal) ?></span>
                        </td></tr>
                </table>

                <?php $labels = json_decode($hasilAi['labels'], true); if ($labels): ?>
                <div class="mt-3">
                    <small class="text-muted d-block mb-1">Label Terdeteksi:</small>
                    <?php foreach ($labels as $l): ?>
                    <span class="badge bg-light border text-dark me-1" style="font-size: .65rem"><?= htmlspecialchars($l) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php if ($pengaduan['latitude'] && $pengaduan['longitude']): ?>
<?php
$lat = (float)$pengaduan['latitude'];
$lng = (float)$pengaduan['longitude'];
$headExtra = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">';
$footerExtra = <<<JS
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const lat = {$lat};
    const lng = {$lng};
    const map = L.map('complaintMap').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Pengaduan').openPopup();
});
</script>
JS;
?>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/admin.php'; ?>
