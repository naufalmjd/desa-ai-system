<?php $pageTitle = 'Profil Admin'; ?>
<?php ob_start(); ?>

<style>
    .profile-banner {
        height: 120px;
        background: linear-gradient(135deg, #1e4080 0%, #059669 100%);
        position: relative;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }
    
    .profile-avatar-container {
        position: relative;
        margin-top: -60px;
        z-index: 2;
    }
    
    .profile-avatar {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .profile-avatar:hover {
        transform: scale(1.05);
    }
    
    .info-item-card {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.25s ease;
        height: 100%;
    }
    
    .info-item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(5, 150, 105, 0.05) !important;
        background: #ffffff;
        border-color: rgba(5, 150, 105, 0.15);
    }
    
    .info-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    
    .info-icon-primary {
        background: rgba(30, 64, 128, 0.08);
        color: #1e4080;
    }
    
    .info-icon-success {
        background: rgba(5, 150, 105, 0.08);
        color: #059669;
    }
    
    .official-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border-radius: 30px;
        padding: 5px 12px;
    }
    
    .btn-edit-profile {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
    }
    
    .btn-edit-profile:hover {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(5, 150, 105, 0.3);
    }
</style>

<div class="row g-4" data-aos="fade-up">
    <!-- Profil Card -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
            <div class="profile-banner">
                <span class="official-badge"><i class="bi bi-shield-check me-1"></i> Staff Aktif</span>
            </div>
            <div class="card-body text-center pt-0 px-4 pb-4">
                <div class="profile-avatar-container">
                    <?php if (!empty($user['foto_profil'])): ?>
                        <img src="<?= APP_URL ?>/public/uploads/profil/<?= htmlspecialchars($user['foto_profil']) ?>" 
                             class="rounded-circle profile-avatar mx-auto mb-3" 
                             alt="Foto Profil">
                    <?php else: ?>
                        <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center fw-bold profile-avatar mx-auto mb-3"
                             style="font-size: 2.5rem">
                            <?= strtoupper(substr($user['nama'] ?? 'A', 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user['nama'] ?? '') ?></h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 mb-3" style="font-size: .75rem">
                    <?= strtoupper($user['role'] ?? 'Admin') ?>
                </span>
                
                <button type="button" class="btn btn-edit-profile btn-sm w-100 mb-3 py-2.5 rounded-3 fw-semibold btn-trigger-edit-profil">
                    <i class="bi bi-pencil-square me-2"></i>Ubah Akun Profil
                </button>
                
                <hr class="my-3 text-muted opacity-25">
                
                <div class="text-start">
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <div class="bg-light rounded-3 p-2 d-flex align-items-center justify-content-center text-muted" style="width: 38px; height: 38px;">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Username</small>
                            <span class="text-dark fw-semibold" style="font-size: 0.88rem;"><?= htmlspecialchars($user['username'] ?? '') ?></span>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <div class="bg-light rounded-3 p-2 d-flex align-items-center justify-content-center text-muted" style="width: 38px; height: 38px;">
                            <i class="bi bi-envelope-fill fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Alamat Email</small>
                            <span class="text-dark fw-semibold" style="font-size: 0.88rem;"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light rounded-3 p-2 d-flex align-items-center justify-content-center text-muted" style="width: 38px; height: 38px;">
                            <i class="bi bi-award-fill fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Jabatan</small>
                            <span class="text-dark fw-semibold" style="font-size: 0.88rem;">Staf Administrasi Desa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Pegawai -->
    <div class="col-lg-8">
        <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-4 border-0 d-flex align-items-center justify-content-between px-4">
                <span class="fw-bold text-dark fs-6">
                    <i class="bi bi-person-badge-fill me-2 text-primary"></i>Data Pegawai Resmi
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5" style="font-size: 0.7rem;">
                    <i class="bi bi-check2-circle me-1"></i>Sesuai Data Dukcapil
                </span>
            </div>
            <div class="card-body pt-0 px-4 pb-4">
                <div class="row g-3">
                    <!-- NIK -->
                    <div class="col-md-6">
                        <div class="info-item-card d-flex align-items-center gap-3">
                            <div class="info-icon info-icon-primary">
                                <i class="bi bi-fingerprint"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem; margin-bottom: 2px;">Nomor Induk Kependudukan (NIK)</small>
                                <strong class="text-primary font-monospace fs-6"><?= htmlspecialchars($penduduk['nik'] ?? '—') ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- KK -->
                    <div class="col-md-6">
                        <div class="info-item-card d-flex align-items-center gap-3">
                            <div class="info-icon info-icon-primary">
                                <i class="bi bi-file-earmark-person"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem; margin-bottom: 2px;">Nomor Kartu Keluarga (KK)</small>
                                <strong class="text-dark font-monospace fs-6"><?= htmlspecialchars($penduduk['no_kk'] ?? '—') ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TTL -->
                    <div class="col-md-6">
                        <div class="info-item-card d-flex align-items-center gap-3">
                            <div class="info-icon info-icon-success">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem; margin-bottom: 2px;">Tempat, Tanggal Lahir</small>
                                <strong class="text-dark"><?= htmlspecialchars($penduduk['tempat_lahir'] ?? '—') ?>, <?= isset($penduduk['tanggal_lahir']) ? date('d/m/Y', strtotime($penduduk['tanggal_lahir'])) : '—' ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Jenis Kelamin -->
                    <div class="col-md-6">
                        <div class="info-item-card d-flex align-items-center gap-3">
                            <div class="info-icon info-icon-success">
                                <i class="bi bi-gender-ambiguous"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem; margin-bottom: 2px;">Jenis Kelamin</small>
                                <strong class="text-dark"><?= ($penduduk['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alamat -->
                    <div class="col-12">
                        <div class="info-item-card d-flex align-items-start gap-3">
                            <div class="info-icon info-icon-primary mt-1">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem; margin-bottom: 2px;">Alamat Lengkap KTP</small>
                                <strong class="text-dark" style="line-height: 1.6; display: inline-block;">
                                    <?= htmlspecialchars($penduduk['alamat'] ?? '—') ?>, RT <?= htmlspecialchars($penduduk['rt'] ?? '—') ?>/RW <?= htmlspecialchars($penduduk['rw'] ?? '—') ?>, Dusun <?= htmlspecialchars($penduduk['dusun'] ?? '—') ?>, Kec. <?= htmlspecialchars($penduduk['kecamatan'] ?? '—') ?>, <?= htmlspecialchars($penduduk['kabupaten'] ?? '—') ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/admin.php'; ?>
