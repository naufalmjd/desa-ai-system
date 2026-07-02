<?php $pageTitle = 'Ajukan Surat Baru'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Form Pengajuan -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-file-earmark-plus-fill me-2 text-primary"></i>Formulir Pengajuan Surat Administrasi
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/warga/surat/store" id="suratForm" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jenis Surat Administrasi <span class="text-danger">*</span></label>
                        <select name="jenis_surat_id" id="jenisSuratSelect" class="form-select" required>
                            <option value="">— Pilih Jenis Surat —</option>
                            <?php foreach ($jenisSurat as $js): ?>
                            <option value="<?= $js['id'] ?>" data-estimasi="<?= $js['estimasi_hari'] ?>" data-syarat='<?= htmlspecialchars($js['persyaratan']) ?>'>
                                <?= htmlspecialchars($js['nama']) ?> (Estimasi: <?= $js['estimasi_hari'] ?> Hari)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keperluan <span class="text-danger">*</span></label>
                        <textarea name="keperluan" class="form-control" rows="3" placeholder="Tuliskan keperluan pembuatan surat secara lengkap..." required maxlength="500"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan opsional untuk admin verifikator..." maxlength="500"></textarea>
                    </div>

                    <!-- Persyaratan Upload List (Dinamis dari JS) -->
                    <div id="persyaratanSection" class="d-none mb-4">
                        <h6 class="fw-bold small border-bottom pb-2 mb-3">Upload Berkas Persyaratan Administrasi</h6>
                        <div id="syaratInputsContainer" class="row g-3"></div>
                    </div>

                    <button type="submit" class="btn btn-primary fw-bold w-100 py-3" id="submitBtn">
                        <i class="bi bi-send-fill me-2"></i> Kirim Pengajuan Surat
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Informasi/Estimasi Pelayanan -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Pelayanan
            </div>
            <div class="card-body" style="font-size: .85rem">
                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-clock-history text-primary fs-5"></i>
                    <div>
                        <strong class="d-block text-dark">Estimasi Waktu Kerja</strong>
                        <span class="text-muted" id="estimasiWaktu">Pilih jenis surat terlebih dahulu untuk mengetahui estimasi waktu.</span>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <div>
                        <strong class="d-block text-dark">Verifikasi Berkas</strong>
                        <span class="text-muted">Pastikan dokumen KTP/KK yang diunggah valid, jelas terbaca, dan tidak terpotong.</span>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-qr-code text-dark fs-5"></i>
                    <div>
                        <strong class="d-block text-dark">Tanda Tangan QR Code</strong>
                        <span class="text-muted">Seluruh surat diproses secara paperless dengan TTD digital resmi Kepala Desa.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$footerExtra = <<<JS
<script>
document.getElementById('jenisSuratSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const container = document.getElementById('syaratInputsContainer');
    const section = document.getElementById('persyaratanSection');
    const estimasiText = document.getElementById('estimasiWaktu');
    
    container.innerHTML = '';
    
    if (!this.value) {
        section.classList.add('d-none');
        estimasiText.textContent = 'Pilih jenis surat terlebih dahulu untuk mengetahui estimasi waktu.';
        return;
    }
    
    section.classList.remove('d-none');
    
    // Set estimasi
    const estimasi = selected.getAttribute('data-estimasi');
    estimasiText.textContent = 'Estimasi penyelesaian surat ini adalah ' + estimasi + ' hari kerja setelah berkas diverifikasi.';
    
    // Parse persyaratan
    const persyaratan = JSON.parse(selected.getAttribute('data-syarat') || '[]');
    
    if (persyaratan.length === 0) {
        container.innerHTML = '<div class="col-12 text-muted small">Tidak ada berkas persyaratan fisik wajib yang perlu diunggah.</div>';
        return;
    }
    
    persyaratan.forEach(syarat => {
        // Map syarat label ke name input
        let inputName = 'lampiran_pendukung[]';
        let isKtpOrKk = syarat.toLowerCase().includes('ktp') ? 'ktp' : (syarat.toLowerCase().includes('kk') ? 'kk' : 'pendukung');
        
        const col = document.createElement('div');
        col.className = 'col-md-6';
        col.innerHTML = `
            <div class="border rounded p-3 bg-light">
                <label class="form-label fw-semibold small text-dark">\${syarat} <span class="text-danger">*</span></label>
                <input type="file" name="files[\${isKtpOrKk}]" class="form-control form-control-sm" accept="application/pdf,image/jpeg,image/png" required>
                <small class="text-muted" style="font-size: .68rem">Format: PDF/JPG/PNG (Maks. 5MB)</small>
            </div>
        `;
        container.appendChild(col);
    });
});

document.getElementById('suratForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim Pengajuan...';
    btn.disabled = true;
});
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/warga.php'; ?>
