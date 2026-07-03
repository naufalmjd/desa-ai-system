<?php $pageTitle = 'Tinjau Pengajuan Surat'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Detail Surat -->
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-file-earmark-text-fill me-2 text-warning"></i>Detail Pengajuan Surat
            </div>
            <div class="card-body">
                <table class="table table-borderless align-middle" style="font-size: .85rem">
                    <tr><td class="text-muted" style="width: 150px">No. Registrasi</td>
                        <td><code class="text-primary font-monospace fs-6"><?= htmlspecialchars($surat['nomor']) ?></code></td></tr>
                    <tr><td class="text-muted">Jenis Surat</td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($surat['jenis_nama'] ?? $surat['jenis_kode']) ?></td></tr>
                    <tr><td class="text-muted">Nama Pemohon</td>
                        <td class="fw-semibold"><?= htmlspecialchars($surat['pemohon_nama']) ?></td></tr>
                    <tr><td class="text-muted">NIK Pemohon</td>
                        <td><code class="text-dark"><?= htmlspecialchars($surat['nik']) ?></code></td></tr>
                    <tr><td class="text-muted">Alamat</td>
                        <td class="text-muted"><?= htmlspecialchars($surat['alamat'] ?? '-') ?>, RT <?= htmlspecialchars($surat['rt'] ?? '-') ?>/RW <?= htmlspecialchars($surat['rw'] ?? '-') ?>, <?= htmlspecialchars($surat['dusun'] ?? '-') ?></td></tr>
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
                    <tr><td class="text-muted">Catatan Verifikator</td>
                        <td class="text-warning fw-medium"><?= htmlspecialchars($surat['catatan_admin'] ?? '—') ?></td></tr>
                    <tr><td class="text-muted">Tanggal Diajukan</td>
                        <td><?= date('d F Y H:i', strtotime($surat['created_at'])) ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Aksi TTD -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-shield-lock-fill me-2 text-success"></i>Tindakan / Persetujuan Kepala Desa
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0 rounded-3 d-flex gap-2 mb-4" style="font-size: .8rem">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <span>Menyetujui surat ini akan membubuhkan **Tanda Tangan Elektronik QR Code** Anda pada berkas PDF akhir secara otomatis.</span>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <button class="btn btn-success fw-bold w-100 py-3" onclick="approveSurat(<?= $surat['id'] ?>)">
                            <i class="bi bi-check-circle-fill me-2"></i> TTD & Setujui Surat
                        </button>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-outline-danger fw-bold w-100 py-3" onclick="rejectSurat(<?= $surat['id'] ?>)">
                            <i class="bi bi-x-circle-fill me-2"></i> Tolak Pengajuan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lampiran Pendukung -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-paperclip me-2 text-primary"></i>Berkas Lampiran Pendukung
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($lampiran as $l): ?>
                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                            <div>
                                <small class="fw-semibold d-block text-dark" style="font-size:.8rem"><?= htmlspecialchars($l['jenis_lampiran'] === 'ktp' ? 'Fotokopi KTP' : ($l['jenis_lampiran'] === 'kk' ? 'Fotokopi Kartu Keluarga' : ($l['jenis_lampiran'] === 'surat_isi' ? 'Dokumen Surat (Isian Warga)' : 'Dokumen Pendukung'))) ?></small>
                                <span class="text-muted font-monospace" style="font-size:.65rem"><?= number_format($l['ukuran'] / 1024, 1) ?> KB</span>
                            </div>
                        </div>
                        <a href="<?= APP_URL ?><?= htmlspecialchars($l['path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary px-3">
                            <i class="bi bi-eye"></i> Buka
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($lampiran)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-x fs-2 d-block mb-1"></i>
                        Tidak ada berkas pendukung terunggah.
                    </div>
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
function approveSurat(id) {
    Swal.fire({
        title: 'Bubuhi TTD & Setujui?',
        text: 'Apakah Anda yakin menyetujui pengajuan surat ini?',
        icon: 'question',
        input: 'textarea',
        inputPlaceholder: 'Tulis catatan persetujuan (opsional)...',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Tanda Tangani!',
        preConfirm: catatan => {
            return $.post(APP_URL + '/kepaladesa/surat/setujui/' + id, {
                _csrf_token: CSRF_TOKEN,
                catatan: catatan
            });
        }
    }).then(result => {
        if (result.value?.success) {
            Swal.fire('Disetujui!', 'Surat berhasil ditandatangani.', 'success')
                .then(() => window.location.href = APP_URL + '/kepaladesa/surat');
        } else if (result.value) {
            Swal.fire('Gagal', result.value.message, 'error');
        }
    });
}

function rejectSurat(id) {
    Swal.fire({
        title: 'Tolak Pengajuan Surat?',
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Tulis alasan penolakan (wajib)...',
        inputAttributes: { required: true },
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Tolak Surat',
        preConfirm: alasan => {
            if (!alasan) {
                Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                return false;
            }
            return fetch(APP_URL + '/kepaladesa/surat/tolak/' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: '_csrf_token=' + CSRF_TOKEN + '&alasan=' + encodeURIComponent(alasan)
            }).then(r => r.json());
        }
    }).then(result => {
        if (result.value?.success) {
            Swal.fire('Ditolak!', 'Pengajuan surat telah ditolak.', 'success')
                .then(() => window.location.href = APP_URL + '/kepaladesa/surat');
        } else if (result.value) {
            Swal.fire('Gagal', result.value.message, 'error');
        }
    });
}
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/kepaladesa.php'; ?>
