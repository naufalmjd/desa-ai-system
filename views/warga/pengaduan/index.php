<?php $pageTitle = 'Riwayat Pengaduan'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card" data-aos="fade-up">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-megaphone-fill me-2 text-warning"></i>Daftar Pengaduan Anda</span>
        <a href="<?= APP_URL ?>/warga/pengaduan/create" class="btn btn-sm btn-warning fw-bold text-white">
            <i class="bi bi-plus-lg me-1"></i> Buat Laporan
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Pengaduan</th>
                        <th>Judul Laporan</th>
                        <th>Kategori</th>
                        <th>Tanggal Kejadian</th>
                        <th>Prioritas AI</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $p): ?>
                    <tr>
                        <td><code class="text-danger"><?= htmlspecialchars($p['nomor']) ?></code></td>
                        <td class="fw-semibold text-truncate" style="max-width:180px"><?= htmlspecialchars($p['judul']) ?></td>
                        <td>
                            <span class="badge bg-light border text-muted">
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $p['kategori']))) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                        <td>
                            <?php
                            $priColors = [
                                'kritis' => 'danger',
                                'tinggi' => 'warning',
                                'sedang' => 'primary',
                                'rendah' => 'success'
                            ];
                            $pri = $p['prioritas_ai'] ?? 'sedang';
                            $c = $priColors[$pri];
                            ?>
                            <span class="badge bg-<?= $c ?>-subtle text-<?= $c ?> border border-<?= $c ?>-subtle badge-status">
                                <?= strtoupper($pri) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusConfig = [
                                'menunggu'          => ['bg-warning-subtle text-warning border-warning-subtle', 'Menunggu'],
                                'ditindaklanjuti'   => ['bg-info-subtle text-info border-info-subtle',          'Ditindaklanjuti'],
                                'diproses'          => ['bg-purple-subtle text-purple border-purple-subtle',      'Diproses'],
                                'selesai'           => ['bg-success-subtle text-success border-success-subtle',   'Selesai'],
                                'ditutup'           => ['bg-secondary-subtle text-secondary border-secondary-subtle', 'Ditutup']
                            ];
                            [$cls, $lbl] = $statusConfig[$p['status']] ?? ['bg-secondary-subtle text-secondary', $p['status']];
                            ?>
                            <span class="badge border <?= $cls ?> badge-status"><?= $lbl ?></span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/warga/pengaduan/show/<?= $p['id'] ?>" class="btn btn-xs btn-outline-primary py-1 px-2" style="font-size: .7rem">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($result['data'])): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-megaphone fs-2 d-block mb-2"></i>
                            Belum ada laporan pengaduan yang diajukan.
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
                    <a class="page-link" href="?page=<?= $i ?>">
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
