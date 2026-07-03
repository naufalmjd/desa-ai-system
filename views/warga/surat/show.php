<?php $pageTitle = 'Detail Pengajuan Surat'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Detail Data -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-file-earmark-text-fill me-2 text-primary"></i>Informasi Detail Surat
            </div>
            <div class="card-body">
                <table class="table table-borderless align-middle" style="font-size: .85rem">
                    <tr><td class="text-muted" style="width: 160px">Nomor Surat</td>
                        <td><code class="text-primary font-monospace fs-6"><?= htmlspecialchars($surat['nomor']) ?></code></td></tr>
                    <tr><td class="text-muted">Jenis Surat</td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($surat['jenis_nama']) ?></td></tr>
                    <tr><td class="text-muted">Tujuan / Keperluan</td>
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
                    <tr><td class="text-muted">Verifikator Admin</td>
                        <td><?= htmlspecialchars($surat['catatan_admin'] ?? 'Belum ada catatan') ?></td></tr>
                    <tr><td class="text-muted">Persetujuan Kades</td>
                        <td><?= htmlspecialchars($surat['catatan_kades'] ?? 'Belum ada catatan') ?></td></tr>
                    <tr><td class="text-muted">Status Pengajuan</td>
                        <td>
                            <?php
                            $statusConfig = [
                                'menunggu'              => ['bg-warning-subtle text-warning border-warning-subtle',   'Menunggu Verifikasi'],
                                'diverifikasi'          => ['bg-info-subtle text-info border-info-subtle',            'Telah Diverifikasi'],
                                'diproses'              => ['bg-purple-subtle text-purple border-purple-subtle',      'Sedang Diproses'],
                                'menunggu_persetujuan'  => ['bg-warning-subtle text-warning border-warning-subtle',   'Menunggu TTD Kades'],
                                'disetujui'             => ['bg-success-subtle text-success border-success-subtle',   'Disetujui'],
                                'selesai'               => ['bg-success-subtle text-success border-success-subtle',   'Selesai & Siap Diunduh'],
                                'ditolak'               => ['bg-danger-subtle text-danger border-danger-subtle',      'Ditolak'],
                            ];
                            [$cls, $lbl] = $statusConfig[$surat['status']] ?? ['bg-secondary-subtle text-secondary', $surat['status']];
                            ?>
                            <span class="badge border <?= $cls ?> badge-status"><?= $lbl ?></span>
                        </td></tr>
                </table>
            </div>
        </div>

        <!-- Download Section -->
        <?php if ($surat['status'] === 'selesai'): ?>
        <div class="card border-success border-opacity-25" style="background: #f0fdf4">
            <div class="card-body p-4 text-center">
                <i class="bi bi-file-earmark-pdf-fill text-success fs-1 mb-2 d-block"></i>
                <h5 class="fw-bold text-success mb-1">Surat Anda Telah Selesai Diproses!</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width:440px">Surat administrasi Anda telah ditandatangani secara digital oleh Kepala Desa dan siap dicetak atau disimpan sebagai PDF resmi.</p>
                <a href="<?= APP_URL ?>/warga/surat/print/<?= $surat['id'] ?>" target="_blank" class="btn btn-success fw-bold px-4 py-2" style="border-radius:10px">
                    <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF Resmi
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Lampiran Terunggah -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-paperclip me-2 text-primary"></i>Lampiran Dokumen Persyaratan
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($lampiran as $l): ?>
                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                            <div>
                                <small class="fw-semibold d-block text-dark" style="font-size:.78rem"><?= htmlspecialchars(strtoupper($l['jenis_lampiran'])) ?></small>
                                <span class="text-muted font-monospace" style="font-size:.65rem"><?= number_format($l['ukuran'] / 1024, 1) ?> KB</span>
                            </div>
                        </div>
                        <a href="<?= APP_URL ?><?= htmlspecialchars($l['path']) ?>" target="_blank" class="btn btn-xs btn-outline-primary px-2">Lihat</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <a href="<?= APP_URL ?>/warga/surat" class="btn btn-light w-100 py-2" style="border-radius:10px">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
