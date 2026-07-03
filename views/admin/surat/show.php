<?php $pageTitle = 'Verifikasi Pengajuan Surat'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Detail Surat -->
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-file-earmark-text-fill me-2 text-primary"></i>Rincian Pengajuan Surat Warga
            </div>
            <div class="card-body">
                <table class="table table-borderless align-middle" style="font-size: .85rem">
                    <tr><td class="text-muted" style="width: 150px">No. Registrasi</td>
                        <td><code class="text-primary font-monospace fs-6"><?= htmlspecialchars($surat['nomor']) ?></code></td></tr>
                    <tr><td class="text-muted">Jenis Surat</td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($surat['jenis_nama']) ?></td></tr>
                    <tr><td class="text-muted">Nama Pemohon</td>
                        <td class="fw-semibold"><?= htmlspecialchars($surat['pemohon_nama']) ?></td></tr>
                    <tr><td class="text-muted">NIK Pemohon</td>
                        <td><code class="text-dark"><?= htmlspecialchars($surat['nik']) ?></code></td></tr>
                    <tr><td class="text-muted">Keperluan</td>
                        <td><?= nl2br(htmlspecialchars($surat['keperluan'])) ?></td></tr>
                    <?php if (!empty($surat['isi_surat'])): ?>
                    <tr><td class="text-muted">Isi Surat Template</td>
                        <td>
                            <div class="border rounded p-3 bg-light font-monospace" style="font-size: .82rem; white-space: pre-wrap; line-height: 1.5; max-height: 250px; overflow-y: auto;">
                                <?= htmlspecialchars(htmlspecialchars_decode($surat['isi_surat'])) ?>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                    <tr><td class="text-muted">Catatan Pemohon</td>
                        <td class="text-muted font-italic"><?= htmlspecialchars($surat['catatan_pemohon'] ?? '—') ?></td></tr>
                    <tr><td class="text-muted">Tanggal Diajukan</td>
                        <td><?= date('d F Y H:i', strtotime($surat['created_at'])) ?></td></tr>
                    <tr><td class="text-muted">Status Terkini</td>
                        <td>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle badge-status">
                                <?= strtoupper($surat['status']) ?>
                            </span>
                        </td></tr>
                </table>
            </div>
        </div>

        <!-- Tindakan Verifikasi -->
        <?php if (in_array($surat['status'], ['menunggu', 'diverifikasi', 'diproses'])): ?>
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-gear-fill me-2 text-warning"></i>Tindakan Staf Administrasi / Verifikator
            </div>
            <div class="card-body">
                <form id="actionForm" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan / Keterangan Verifikasi</label>
                        <textarea name="catatan" id="catatanInput" class="form-control" rows="3" placeholder="Tuliskan catatan verifikasi berkas atau revisi jika ditolak..."><?= htmlspecialchars($surat['catatan_admin'] ?? '') ?></textarea>
                    </div>

                    <div class="row g-2">
                        <?php if ($surat['status'] === 'menunggu'): ?>
                        <div class="col-sm-4">
                            <button type="button" onclick="submitVerifikasi()" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Verifikasi Berkas
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($surat['status'] === 'diverifikasi' || $surat['status'] === 'diproses'): ?>
                        <div class="col-sm-4">
                            <button type="button" onclick="submitKirimKades()" class="btn btn-success w-100 py-2 fw-semibold">
                                <i class="bi bi-send me-1"></i> Teruskan ke Kades
                            </button>
                        </div>
                        <?php endif; ?>

                        <div class="col-sm-4">
                            <button type="button" onclick="submitTolak()" class="btn btn-outline-danger w-100 py-2 fw-semibold">
                                <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Lampiran -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-paperclip me-2 text-primary"></i>Dokumen Lampiran Warga
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($lampiran as $l): ?>
                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                            <div>
                                <small class="fw-semibold d-block text-dark" style="font-size:.8rem"><?= htmlspecialchars(strtoupper($l['jenis_lampiran'])) ?></small>
                                <span class="text-muted font-monospace" style="font-size:.65rem"><?= number_format($l['ukuran'] / 1024, 1) ?> KB</span>
                            </div>
                        </div>
                        <a href="<?= APP_URL ?><?= htmlspecialchars($l['path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary px-3">
                            <i class="bi bi-eye"></i> Buka
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($lampiran)): ?>
                    <div class="text-center py-5 text-muted small"><i class="bi bi-file-earmark-x fs-2 d-block mb-1"></i>Tidak ada lampiran terunggah</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$footerExtra = <<<JS
<script>
const suratId = {$surat['id']};

function submitVerifikasi() {
    const form = document.getElementById('actionForm');
    form.action = APP_URL + '/admin/surat/verifikasi/' + suratId;
    form.submit();
}

function submitKirimKades() {
    const form = document.getElementById('actionForm');
    form.action = APP_URL + '/admin/surat/kirimkades/' + suratId;
    form.submit();
}

function submitTolak() {
    const alasan = document.getElementById('catatanInput').value.trim();
    if (!alasan) {
        Swal.fire('Catatan Wajib Diisi', 'Silakan tuliskan alasan penolakan pada kolom catatan verifikasi terlebih dahulu.', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Tolak Pengajuan Surat?',
        text: 'Apakah Anda yakin ingin menolak pengajuan surat ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        
        // Buat dynamic form submit ke tolak
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = APP_URL + '/admin/surat/tolak/' + suratId;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_csrf_token';
        csrfInput.value = CSRF_TOKEN;
        form.appendChild(csrfInput);
        
        const alasanInput = document.createElement('input');
        alasanInput.type = 'hidden';
        alasanInput.name = 'alasan';
        alasanInput.value = alasan;
        form.appendChild(alasanInput);
        
        document.body.appendChild(form);
        form.submit();
    });
}
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/admin.php'; ?>
