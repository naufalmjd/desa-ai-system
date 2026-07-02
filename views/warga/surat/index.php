<?php $pageTitle = 'Riwayat Pengajuan Surat'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card" data-aos="fade-up">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <span><i class="bi bi-file-earmark-text-fill me-2 text-primary"></i>Daftar Pengajuan Surat Anda</span>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width:160px" onchange="this.form.submit()">
                    <option value="">— Semua Status —</option>
                    <?php foreach([
                        'menunggu' => 'Menunggu',
                        'diverifikasi' => 'Diverifikasi',
                        'diproses' => 'Diproses',
                        'menunggu_persetujuan' => 'Menunggu TTD',
                        'disetujui' => 'Disetujui',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak'
                    ] as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= $filters['status']===$val?'selected':'' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <a href="<?= APP_URL ?>/warga/surat/create" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Buat Pengajuan
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Jenis Surat</th>
                        <th>Keperluan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Berkas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $s): ?>
                    <tr>
                        <td><code class="text-primary"><?= htmlspecialchars($s['nomor']) ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($s['jenis_nama']) ?></td>
                        <td class="text-muted text-truncate" style="max-width:180px"><?= htmlspecialchars($s['keperluan']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                        <td>
                            <?php
                            $statusConfig = [
                                'menunggu'              => ['bg-warning-subtle text-warning border-warning-subtle',   'Menunggu'],
                                'diverifikasi'          => ['bg-info-subtle text-info border-info-subtle',            'Diverifikasi'],
                                'diproses'              => ['bg-purple-subtle text-purple border-purple-subtle',      'Diproses'],
                                'menunggu_persetujuan'  => ['bg-warning-subtle text-warning border-warning-subtle',   'Menunggu TTD'],
                                'disetujui'             => ['bg-success-subtle text-success border-success-subtle',   'Disetujui'],
                                'selesai'               => ['bg-success-subtle text-success border-success-subtle',   'Selesai'],
                                'ditolak'               => ['bg-danger-subtle text-danger border-danger-subtle',      'Ditolak'],
                            ];
                            [$cls, $lbl] = $statusConfig[$s['status']] ?? ['bg-secondary-subtle text-secondary', $s['status']];
                            ?>
                            <span class="badge border <?= $cls ?> badge-status"><?= $lbl ?></span>
                        </td>
                        <td>
                            <?php if ($s['file_surat']): ?>
                            <a href="<?= APP_URL ?><?= htmlspecialchars($s['file_surat']) ?>" target="_blank" class="btn btn-xs btn-outline-success py-1 px-2" style="font-size: .7rem">
                                <i class="bi bi-file-pdf"></i> Unduh PDF
                            </a>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= APP_URL ?>/warga/surat/show/<?= $s['id'] ?>" class="btn btn-xs btn-outline-primary py-1 px-2" style="font-size: .7rem" title="Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="<?= APP_URL ?>/warga/surat/tracking?id=<?= $s['id'] ?>" class="btn btn-xs btn-outline-info py-1 px-2" style="font-size: .7rem" title="Lacak">
                                    <i class="bi bi-truck"></i> Lacak
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($result['data'])): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-file-earmark-plus fs-2 d-block mb-2"></i>
                            Belum ada riwayat pengajuan surat.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($result['last_page'] > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">
            Menampilkan <?= count($result['data']) ?> dari <?= number_format($result['total']) ?> data
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $result['last_page']; $i++): ?>
                <li class="page-item <?= $i===$result['current_page']?'active':'' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&status=<?= urlencode($filters['status']) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
