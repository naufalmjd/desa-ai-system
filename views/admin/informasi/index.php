<?php $pageTitle = 'Kelola Informasi & Berita'; ?>
<?php ob_start(); ?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex gap-2 rounded-3 mb-3">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Daftar Berita/Pengumuman -->
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-newspaper me-2 text-primary"></i>Kelola Kabar/Berita Desa</span>
                <button class="btn btn-xs btn-primary py-1 px-2" style="font-size: .75rem" data-bs-toggle="modal" data-bs-target="#addBeritaModal"><i class="bi bi-plus-lg"></i> Berita Baru</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th><th>Kategori</th><th>Status</th><th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($berita as $b): ?>
                            <tr>
                                <td class="fw-semibold small text-truncate" style="max-width: 220px"><?= htmlspecialchars($b['judul']) ?></td>
                                <td><span class="badge bg-light border text-dark"><?= htmlspecialchars($b['kategori']) ?></span></td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle badge-status">
                                        <?= strtoupper($b['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($b['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($berita)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4 small">Belum ada berita dibuat</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bantuan Sosial -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-heart-fill me-2 text-danger"></i>Program Bantuan Sosial (Bansos)</span>
                <button class="btn btn-xs btn-outline-danger py-1 px-2" style="font-size: .75rem" data-bs-toggle="modal" data-bs-target="#addBansosModal"><i class="bi bi-plus-lg"></i> Bansos Baru</button>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($bantuan as $bs): ?>
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: .8rem"><?= htmlspecialchars($bs['nama']) ?></h6>
                            <span class="badge bg-success-subtle text-success border border-success-subtle badge-status"><?= strtoupper($bs['status']) ?></span>
                        </div>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($bs['deskripsi']) ?></p>
                        <div class="small mb-1">
                            <span class="text-muted">Sasaran:</span> <strong class="text-dark"><?= htmlspecialchars($bs['sasaran']) ?></strong>
                        </div>
                        <div class="small">
                            <span class="text-muted">Besaran:</span> <strong class="text-primary"><?= htmlspecialchars($bs['besaran']) ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($bantuan)): ?>
                    <div class="text-center py-4 text-muted small">Belum ada program bansos terdaftar</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Berita Baru -->
<div class="modal fade" id="addBeritaModal" tabindex="-1" aria-labelledby="addBeritaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="addBeritaModalLabel">
                    <i class="bi bi-newspaper text-primary me-1.5"></i> Buat Berita / Pengumuman Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= APP_URL ?>/admin/informasi/storeBerita">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <div class="modal-body px-4 py-2">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-secondary">Judul Informasi <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control rounded-3" placeholder="Masukkan judul berita atau pengumuman" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select rounded-3">
                                <option value="berita">Berita Desa</option>
                                <option value="pengumuman">Pengumuman Resmi</option>
                                <option value="agenda">Agenda / Jadwal Kegiatan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Status Publikasi <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3">
                                <option value="publish">Publikasikan (Publish)</option>
                                <option value="draft">Simpan sebagai Draft</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-secondary">Isi Konten Informasi <span class="text-danger">*</span></label>
                            <textarea name="konten" class="form-control rounded-3" rows="8" placeholder="Tulis isi informasi secara detail di sini..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan & Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bansos Baru -->
<div class="modal fade" id="addBansosModal" tabindex="-1" aria-labelledby="addBansosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="addBansosModalLabel">
                    <i class="bi bi-heart-fill text-danger me-1.5"></i> Tambah Program Bansos Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= APP_URL ?>/admin/informasi/storeBansos">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <div class="modal-body px-4 py-2">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-secondary">Nama Program Bansos <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: BLT Kemensos Tahap II" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Sasaran Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="sasaran" class="form-control rounded-3" placeholder="Contoh: Lansia / Keluarga Kurang Mampu" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Besaran Bantuan <span class="text-danger">*</span></label>
                            <input type="text" name="besaran" class="form-control rounded-3" placeholder="Contoh: Rp 300.000 / KK" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Periode Penyaluran <span class="text-danger">*</span></label>
                            <input type="text" name="periode" class="form-control rounded-3" placeholder="Contoh: Juli - September 2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Status Program <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3">
                                <option value="aktif">Aktif / Berjalan</option>
                                <option value="pendaftaran">Tahap Pendaftaran</option>
                                <option value="selesai">Selesai Disalurkan</option>
                                <option value="ditutup">Ditutup</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-secondary">Syarat Penerima Bansos (Satu syarat per baris)</label>
                            <textarea name="syarat" class="form-control rounded-3" rows="3" placeholder="Fotokopi KTP&#10;Fotokopi KK&#10;Surat Keterangan Tidak Mampu"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-secondary">Deskripsi Program <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Tuliskan deskripsi singkat mengenai program bansos ini..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">Simpan Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
