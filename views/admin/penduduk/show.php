<?php $pageTitle = 'Detail Penduduk'; ?>
<?php ob_start(); ?>

<div class="row g-3" data-aos="fade-up">
    <!-- Left Column: Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center p-4 position-relative">
            <div class="position-absolute start-0 top-0 w-100 h-1.5 bg-primary"></div>
            
            <div class="my-3">
                <span class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 90px; height: 90px;">
                    <i class="bi <?= $penduduk['jenis_kelamin'] === 'L' ? 'bi-gender-male' : 'bi-gender-female' ?>" style="font-size: 3.2rem;"></i>
                </span>
            </div>

            <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($penduduk['nama']) ?></h5>
            <code class="text-primary fs-6 mb-3 d-block"><?= htmlspecialchars($penduduk['nik']) ?></code>

            <?php
            $sc = match($penduduk['status_penduduk']) {
                'Tetap'     => 'success',
                'Sementara' => 'warning',
                'Pindah'    => 'secondary',
                'Meninggal' => 'dark',
                default     => 'secondary',
            };
            ?>
            <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?> border border-<?= $sc ?>-subtle px-3 py-2 mx-auto mb-4" style="font-size: .8rem; border-radius: 8px;">
                Penduduk <?= htmlspecialchars($penduduk['status_penduduk']) ?>
            </span>

            <div class="border-top pt-3 text-start">
                <div class="row g-2">
                    <div class="col-6 small text-secondary">No. KK:</div>
                    <div class="col-6 small fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($penduduk['no_kk']) ?>"><?= htmlspecialchars($penduduk['no_kk']) ?></div>
                    
                    <div class="col-6 small text-secondary">No. HP:</div>
                    <div class="col-6 small fw-semibold text-dark"><?= htmlspecialchars($penduduk['no_hp'] ?: '-') ?></div>

                    <div class="col-6 small text-secondary">Jenis Kelamin:</div>
                    <div class="col-6 small fw-semibold text-dark"><?= $penduduk['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
                </div>
            </div>
            
            <div class="d-grid gap-2 mt-4 pt-3 border-top">
                <a href="<?= APP_URL ?>/admin/penduduk/edit/<?= $penduduk['id'] ?>" class="btn btn-warning text-dark btn-sm fw-semibold" style="border-radius: 8px;">
                    <i class="bi bi-pencil-square me-1"></i> Edit Data
                </a>
                <button onclick="confirmAction('<?= APP_URL ?>/admin/penduduk/delete/<?= $penduduk['id'] ?>', 'Hapus Data?', 'Data penduduk ini akan dihapus permanen.')" class="btn btn-outline-danger btn-sm" style="border-radius: 8px;">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Right Column: Detail Information -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden p-4 position-relative" style="height: 100%;">
            <div class="position-absolute start-0 top-0 w-100 h-1.5 bg-primary"></div>
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-person-lines-fill text-primary me-2"></i> Informasi Detail Penduduk
                </h5>
                <a href="<?= APP_URL ?>/admin/penduduk" class="btn btn-light btn-sm px-3" style="border-radius: 8px;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="row g-4">
                <!-- Personal Info -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-1.5"></i> Profil Pribadi</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-secondary ps-0 py-1.5" style="width: 120px;">Tempat Lahir</td>
                                    <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['tempat_lahir']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary ps-0 py-1.5">Tanggal Lahir</td>
                                    <td class="text-dark fw-medium py-1.5">: <?= date('d F Y', strtotime($penduduk['tanggal_lahir'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary ps-0 py-1.5">Agama</td>
                                    <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['agama']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary ps-0 py-1.5">Status Kawin</td>
                                    <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['status_kawin']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Education & Job -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-briefcase me-1.5"></i> Pekerjaan & Pendidikan</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-secondary ps-0 py-1.5" style="width: 120px;">Pekerjaan</td>
                                    <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['pekerjaan']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary ps-0 py-1.5">Pendidikan</td>
                                    <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['pendidikan']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Address & Location -->
                <div class="col-12 mt-2">
                    <h6 class="fw-bold text-primary mb-3 border-top pt-3"><i class="bi bi-geo-alt me-1.5"></i> Informasi Wilayah & Alamat</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-secondary ps-0 py-1.5" style="width: 120px;">Alamat Rumah</td>
                                        <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['alamat']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary ps-0 py-1.5">Dusun</td>
                                        <td class="text-dark fw-medium py-1.5">: Dusun <?= htmlspecialchars($penduduk['dusun']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary ps-0 py-1.5">RT / RW</td>
                                        <td class="text-dark fw-medium py-1.5">: RT <?= htmlspecialchars($penduduk['rt']) ?> / RW <?= htmlspecialchars($penduduk['rw']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-secondary ps-0 py-1.5" style="width: 120px;">Desa / Kelurahan</td>
                                        <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['desa']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary ps-0 py-1.5">Kecamatan</td>
                                        <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['kecamatan']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary ps-0 py-1.5">Kabupaten / Kota</td>
                                        <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['kabupaten']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary ps-0 py-1.5">Provinsi</td>
                                        <td class="text-dark fw-medium py-1.5">: <?= htmlspecialchars($penduduk['provinsi']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/admin.php'; ?>
