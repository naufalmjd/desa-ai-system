<?php $pageTitle = 'Pelacakan Pengajuan Surat'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Selector Surat -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <i class="bi bi-search me-2 text-primary"></i>Pilih Surat Yang Dilacak
            </div>
            <div class="card-body">
                <form method="GET" action="<?= APP_URL ?>/warga/surat/tracking" class="mb-3">
                    <label class="form-label small text-muted">Nomor Registrasi Surat</label>
                    <select name="id" class="form-select mb-2" onchange="this.form.submit()">
                        <option value="">— Pilih Pengajuan Surat —</option>
                        <?php foreach ($suratList as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($selected && (int)$selected['id'] === (int)$s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['jenis_nama']) ?> (<?= htmlspecialchars($s['nomor']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if (empty($suratList)): ?>
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-file-earmark-x fs-3 d-block mb-1"></i>
                    Belum ada surat terdaftar.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Timeline Pelacakan -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <i class="bi bi-truck me-2 text-primary"></i>Status Lini Masa Pelacakan
            </div>
            <div class="card-body">
                <?php if (!$selected): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-arrow-left-circle-fill text-primary fs-1 mb-2 d-block"></i>
                    <h5>Silakan pilih surat terlebih dahulu</h5>
                    <p class="small">Gunakan panel sebelah kiri untuk memilih nomor surat yang ingin dipantau statusnya.</p>
                </div>
                <?php else: ?>
                
                <div class="mb-4">
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($selected['jenis_nama']) ?></h5>
                    <code class="text-primary font-monospace"><?= htmlspecialchars($selected['nomor']) ?></code>
                </div>

                <!-- Timeline Progress Steps -->
                <div class="position-relative ps-4 ms-2" style="border-left: 2px dashed #cbd5e1">
                    <?php
                    $steps = [
                        ['id' => 'menunggu',             'title' => 'Pengajuan Diterima',     'desc' => 'Surat berhasil diajukan dan masuk ke antrean verifikator.'],
                        ['id' => 'diverifikasi',         'title' => 'Diverifikasi oleh Admin', 'desc' => 'Berkas persyaratan telah diperiksa keabsahannya oleh staf desa.'],
                        ['id' => 'diproses',             'title' => 'Dalam Proses',           'desc' => 'Draf surat sedang disusun dan disiapkan berkas pendukungnya.'],
                        ['id' => 'menunggu_persetujuan', 'title' => 'Menunggu TTD Kades',     'desc' => 'Menunggu persetujuan dan tanda tangan elektronik Kepala Desa.'],
                        ['id' => 'selesai',              'title' => 'Surat Selesai',          'desc' => 'Surat resmi telah diterbitkan dan siap diunduh dalam format PDF.']
                    ];
                    
                    // Hitung current step index
                    $currentStatus = $selected['status'];
                    $currentIdx = 0;
                    if ($currentStatus === 'ditolak') {
                        $currentIdx = -1; // Status ditolak di-handle secara khusus
                    } else {
                        foreach ($steps as $idx => $st) {
                            if ($st['id'] === $currentStatus || ($currentStatus === 'disetujui' && $st['id'] === 'menunggu_persetujuan')) {
                                $currentIdx = $idx;
                                break;
                            }
                        }
                        if ($currentStatus === 'selesai') $currentIdx = 4;
                    }
                    
                    foreach ($steps as $idx => $st):
                        $isActive = $idx <= $currentIdx;
                        $color = $isActive ? 'success' : 'secondary';
                        $icon = $isActive ? 'bi-check-circle-fill' : 'bi-circle';
                        if ($idx === $currentIdx) {
                            $color = 'primary';
                            $icon = 'bi-play-circle-fill';
                        }
                    ?>
                    <div class="position-relative mb-4">
                        <!-- Bullet Icon -->
                        <span class="position-absolute start-0 top-0 translate-middle bg-white p-1" style="left: -18px !important">
                            <i class="bi <?= $icon ?> text-<?= $color ?> fs-5"></i>
                        </span>
                        <div class="ms-3">
                            <h6 class="fw-bold text-<?= $isActive ? 'dark' : 'muted' ?> mb-1"><?= $st['title'] ?></h6>
                            <p class="text-muted small mb-0"><?= $st['desc'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Jika Ditolak -->
                    <?php if ($currentStatus === 'ditolak'): ?>
                    <div class="position-relative mb-4">
                        <span class="position-absolute start-0 top-0 translate-middle bg-white p-1" style="left: -18px !important">
                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                        </span>
                        <div class="ms-3">
                            <h6 class="fw-bold text-danger mb-1">Pengajuan Ditolak</h6>
                            <p class="text-danger small mb-0">Alasan: <?= htmlspecialchars($selected['catatan_admin'] ?: ($selected['catatan_kades'] ?: 'Dokumen tidak valid.')) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
