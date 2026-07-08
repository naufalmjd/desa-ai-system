<?php
if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'superadmin') {
    require VIEW_PATH . '/layouts/superadmin.php';
    return;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<script>
    (function() {
        const theme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-bs-theme', theme);
    })();
</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — <?= htmlspecialchars(APP_NAME ?? '') ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
    --primary:      #10b981;
    --primary-dark: #059669;
    --primary-glow: rgba(16, 185, 129, 0.15);
    --accent:       #34d399;
    --sidebar-bg:   #060b13;
    --sidebar-w:    245px;
    --sidebar-c:    64px;  /* Lebar standar saat collapsed */
    --header-h:     58px;
    --bg:           #080c14;
    --card-bg:      #111827;
    --border:       rgba(255, 255, 255, 0.06);
    --text-main:    #f1f5f9;
    --text-muted:   #94a3b8;
    --topbar-bg:    #111827;
}
* { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
body { background: var(--bg); color: var(--text-main); min-height: 100vh; transition: background 0.3s, color 0.3s; }

/* --- PERBAIKAN STRUKTUR SIDEBAR --- */
.sidebar { 
    width: var(--sidebar-w); 
    height: 100vh; 
    background: var(--sidebar-bg);
    position: fixed; 
    top: 0; 
    left: 0; 
    z-index: 1040; 
    display: flex;
    flex-direction: column; 
    transition: width .25s cubic-bezier(0.4, 0, 0.2, 1); 
    overflow: hidden; 
    border-right: 1px solid var(--border);
}
.sidebar.collapsed { 
    width: var(--sidebar-c); 
}

/* Penanganan Sempurna Saat Sidebar Mengecil (Collapsed) */
.sidebar.collapsed .sb-text,
.sidebar.collapsed .sidebar-user-info, /* Sembunyikan info teks user */
.sidebar.collapsed .sb-nav span,
.sidebar.collapsed .sb-footer span,   /* Solusi Utama: Sembunyikan teks "Keluar" */
.sidebar.collapsed .section-label { 
    opacity: 0;
    pointer-events: none;
    display: none !important; 
}
.sidebar.collapsed .sb-brand {
    justify-content: center;
    padding: 1rem 0;
}
.sidebar.collapsed .sidebar-user {
    justify-content: center;
    padding: 1rem 0;
    margin-left: 0 !important;
    margin-right: 0 !important;
}
.sidebar.collapsed .sb-nav a,
.sidebar.collapsed .sb-footer a {
    justify-content: center;
    padding: 0.6rem 0;
}

.sb-brand { display: flex; align-items: center; gap: .75rem; padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,.05); min-height: var(--header-h); }
.sb-icon { width: 36px; height: 36px; background: rgba(255,255,255,.15); border-radius: 10px;
           display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.sb-text .n { color: #fff; font-weight: 700; font-size: .8rem; white-space: nowrap; }
.sb-text .s { color: rgba(255,255,255,.5); font-size: .68rem; white-space: nowrap; }

.sb-badge { width: 34px; height: 34px; border-radius: 10px; background: var(--primary-dark);
            display: flex; align-items: center; justify-content: center; color: #fff;
            font-weight: 700; font-size: .7rem; flex-shrink: 0; }

.sb-nav { flex: 1; overflow-y: auto; padding: .5rem; }
.sb-nav a { display: flex; align-items: center; gap: .75rem; color: rgba(255,255,255,.6);
            padding: .6rem .75rem; border-radius: 10px; text-decoration: none;
            font-size: .8rem; font-weight: 500; white-space: nowrap; transition: .15s; }
.sb-nav a:hover { background: rgba(255,255,255,.08); color: #fff; }
.sb-nav a.active { background: rgba(255,255,255,.15); color: #fff; }
.sb-nav a i { font-size: 1rem; flex-shrink: 0; width: 20px; text-align: center; } /* Penyelaras Ikon */
.sb-nav .section-label { color: rgba(255,255,255,.3); font-size: .6rem; font-weight: 700;
                          text-transform: uppercase; letter-spacing: .08em; padding: .75rem .75rem .3rem; }

.sb-footer { padding: .75rem; border-top: 1px solid rgba(255,255,255,.05); }
.sb-footer a { display: flex; align-items: center; gap: .75rem; color: #f87171;
               padding: .6rem .75rem; border-radius: 10px; font-size: .8rem;
               font-weight: 500; text-decoration: none; transition: .15s; }
.sb-footer a:hover { background: rgba(239,68,68,.15); }
.sb-footer a i { font-size: 1rem; flex-shrink: 0; width: 20px; text-align: center; }

.main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex;
             flex-direction: column; transition: margin-left .25s ease; }
.main-wrap.collapsed { margin-left: var(--sidebar-c); }

.topbar { height: var(--header-h); background: var(--topbar-bg); border-bottom: 1px solid var(--border);
          display: flex; align-items: center; justify-content: space-between;
          padding: 0 1.25rem; position: sticky; top: 0; z-index: 100; }

.page-content { flex: 1; padding: 1.5rem; }

/* Premium Modernization */
.card {
    transition: transform .25s ease, box-shadow .25s ease;
    background: var(--card-bg) !important;
    color: var(--text-main);
    border: 1px solid var(--border) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(16, 185, 129, 0.15) !important;
}
.card-header {
    background: rgba(255, 255, 255, 0.02) !important;
    border-bottom: 1px solid var(--border) !important;
    color: var(--text-main);
    font-weight: 700;
}
.card-footer {
    background: rgba(255, 255, 255, 0.02) !important;
    border-top: 1px solid var(--border) !important;
    color: var(--text-muted);
}
.table { color: var(--text-main) !important; }
.table th { font-size: .72rem; font-weight: 700; text-transform: uppercase;
             letter-spacing: .04em; color: var(--text-muted); border-bottom: 2px solid var(--border); background: transparent !important; }
.table td { font-size: .8rem; vertical-align: middle; border-bottom: 1px solid var(--border); background: transparent !important; color: var(--text-main); }
.table-hover tbody tr:hover td { background-color: rgba(255, 255, 255, 0.02) !important; }

.app-footer { background: var(--topbar-bg); border-top: 1px solid var(--border);
               padding: .5rem 1.25rem; font-size: .68rem; color: var(--text-muted); }

/* Form Inputs in Dark Mode */
.form-control, .form-select, .input-group-text {
    background-color: #1f2937 !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #f3f4f6 !important;
    border-radius: 10px;
}
.form-control:focus, .form-select:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25) !important;
    background-color: #1f2937 !important;
    color: #f3f4f6 !important;
}
.form-control:disabled, .form-select:disabled {
    background-color: rgba(255, 255, 255, 0.05) !important;
    color: var(--text-muted) !important;
    opacity: 0.7;
}
.form-control::placeholder {
    color: var(--text-muted) !important;
    opacity: 0.5;
}

/* Modals in Dark Mode */
.modal-content {
    background-color: var(--card-bg) !important;
    border: 1px solid var(--border) !important;
    color: var(--text-main);
}
.modal-header, .modal-footer {
    border-color: var(--border) !important;
}
.modal-header .btn-close {
    filter: invert(1);
}

@media (max-width: 991px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.mobile-open { transform: translateX(0); }
    .main-wrap { margin-left: 0 !important; }
    .sidebar-overlay { display: block !important; }
}
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1039; }

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    box-shadow: 0 4px 15px var(--primary-glow);
    transition: all .25s ease;
    color: #fff !important;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}
.sidebar-user {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.06);
}
.sb-icon {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
}
.sb-icon i {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

/* ── STYLE MODAL EDIT PROFIL (WHATSAPP STYLE) ── */
.wa-input-group {
    position: relative; border-bottom: 1px solid var(--border); padding-bottom: 0.25rem; margin-bottom: 1.25rem;
    display: flex; align-items: center; justify-content: space-between;
}
.wa-input-group input:disabled {
    background: transparent !important; border: none !important; color: var(--text-main) !important;
    font-weight: 500; padding-left: 0; box-shadow: none !important; cursor: default;
}
.wa-input-group input:not(:disabled) {
    border: none !important; border-bottom: 2px solid var(--primary) !important; border-radius: 0 !important;
    padding-left: 0; box-shadow: none !important; color: #fff !important;
}
.wa-btn-pencil { background: none; border: none; color: var(--text-muted); padding: 4px 8px; transition: color 0.2s; }
.wa-btn-pencil:hover { color: var(--primary); }
.btn-trigger-edit-profil { cursor: pointer; transition: transform 0.2s; color: var(--primary); }
.btn-trigger-edit-profil:hover { transform: scale(1.15); }

/* --- LIGHT MODE OVERRIDES & VARIABLES --- */
[data-bs-theme="light"] {
    --bg:           #eef2f7;
    --card-bg:      #ffffff;
    --border:       rgba(30,64,128,.1);
    --text-main:    #0f172a;
    --text-muted:   #5a6a82;
    --topbar-bg:    #ffffff;
}
[data-bs-theme="light"] body {
    background: var(--bg) !important;
    color: var(--text-main) !important;
}
[data-bs-theme="light"] .topbar {
    background: var(--topbar-bg) !important;
    border-bottom: 1px solid var(--border) !important;
}
[data-bs-theme="light"] .app-footer {
    background: var(--topbar-bg) !important;
    border-top: 1px solid var(--border) !important;
    color: var(--text-muted) !important;
}
[data-bs-theme="light"] .card {
    background: var(--card-bg) !important;
    border-color: var(--border) !important;
    color: var(--text-main) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
}
[data-bs-theme="light"] .card-header {
    background: rgba(0, 0, 0, 0.01) !important;
    border-bottom: 1px solid var(--border) !important;
    color: var(--text-main) !important;
}
[data-bs-theme="light"] .card-footer {
    background: rgba(0, 0, 0, 0.01) !important;
    border-top: 1px solid var(--border) !important;
    color: var(--text-muted) !important;
}
[data-bs-theme="light"] .table {
    color: var(--text-main) !important;
}
[data-bs-theme="light"] .table th {
    color: var(--text-muted) !important;
    border-bottom: 2px solid var(--border) !important;
}
[data-bs-theme="light"] .table td {
    color: var(--text-main) !important;
    border-bottom: 1px solid var(--border) !important;
}
[data-bs-theme="light"] .table-hover tbody tr:hover td {
    background-color: rgba(0, 0, 0, 0.02) !important;
}
[data-bs-theme="light"] .form-control,
[data-bs-theme="light"] .form-select,
[data-bs-theme="light"] .input-group-text {
    background-color: #ffffff !important;
    border: 1px solid #d1d5db !important;
    color: #1f2937 !important;
}
[data-bs-theme="light"] .form-control:focus,
[data-bs-theme="light"] .form-select:focus {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border-color: var(--primary) !important;
}
[data-bs-theme="light"] .form-control:disabled,
[data-bs-theme="light"] .form-select:disabled {
    background-color: #f3f4f6 !important;
    color: #9ca3af !important;
}
[data-bs-theme="light"] .modal-content {
    background-color: var(--card-bg) !important;
    border: 1px solid var(--border) !important;
    color: var(--text-main) !important;
}
[data-bs-theme="light"] .modal-header .btn-close {
    filter: none !important;
}
[data-bs-theme="light"] .wa-input-group input:disabled {
    color: var(--text-muted) !important;
}
[data-bs-theme="light"] .wa-input-group input:not(:disabled) {
    color: var(--text-main) !important;
}
[data-bs-theme="light"] .text-dark {
    color: #0f172a !important;
}
[data-bs-theme="light"] .text-secondary, [data-bs-theme="light"] .text-muted {
    color: var(--text-muted) !important;
}
[data-bs-theme="light"] .btn-light {
    background-color: #f3f4f6 !important;
    border-color: #e5e7eb !important;
    color: #1f2937 !important;
}
[data-bs-theme="light"] .btn-light:hover {
    background-color: #e5e7eb !important;
}

/* --- Purple Status Badge Styling --- */
.bg-purple-subtle { background-color: rgba(139, 92, 246, 0.1) !important; }
.text-purple { color: #7c3aed !important; }
.border-purple-subtle { border-color: rgba(139, 92, 246, 0.2) !important; }

[data-bs-theme="dark"] .bg-purple-subtle { background-color: rgba(139, 92, 246, 0.18) !important; }
[data-bs-theme="dark"] .text-purple { color: #a78bfa !important; }
[data-bs-theme="dark"] .border-purple-subtle { border-color: rgba(139, 92, 246, 0.3) !important; }

/* Light Theme General Badge Overrides */
[data-bs-theme="light"] .badge:not([class*="text-"]):not([class*="bg-"]) {
    color: #1f2937 !important;
}
[data-bs-theme="light"] .badge-status.bg-secondary-subtle {
    color: #4b5563 !important;
    background-color: #f3f4f6 !important;
    border-color: #e5e7eb !important;
}

/* --- HIGH CONTRAST THEME OVERRIDES --- */
[data-bs-theme="dark"] .text-dark,
[data-bs-theme="dark"] .text-black,
[data-bs-theme="dark"] strong.text-dark,
[data-bs-theme="dark"] span.text-dark,
[data-bs-theme="dark"] div.text-dark,
[data-bs-theme="dark"] td.text-dark {
    color: #f3f4f6 !important;
}
[data-bs-theme="dark"] .text-muted,
[data-bs-theme="dark"] .text-secondary {
    color: #9ca3af !important;
}
[data-bs-theme="dark"] .bg-light,
[data-bs-theme="dark"] .badge.bg-light,
[data-bs-theme="dark"] .bg-light.text-dark {
    background-color: #1f2937 !important;
    color: #cbd5e1 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
[data-bs-theme="dark"] .info-item-card {
    background: rgba(255, 255, 255, 0.02) !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
}
[data-bs-theme="dark"] .info-item-card:hover {
    background: rgba(255, 255, 255, 0.05) !important;
    border-color: var(--primary) !important;
}
[data-bs-theme="dark"] div[style*="background-color: #f0f9ff"],
[data-bs-theme="dark"] div[style*="background: #f0f9ff"] {
    background-color: rgba(14, 165, 233, 0.1) !important;
    color: #38bdf8 !important;
    border-color: rgba(14, 165, 233, 0.2) !important;
}
[data-bs-theme="dark"] div[style*="background-color: #f0fdf4"],
[data-bs-theme="dark"] div[style*="background: #f0fdf4"] {
    background-color: rgba(16, 185, 129, 0.1) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.2) !important;
}
[data-bs-theme="dark"] .modal-title,
[data-bs-theme="dark"] .modal-content h5,
[data-bs-theme="dark"] .modal-content h6,
[data-bs-theme="dark"] .card-title,
[data-bs-theme="dark"] h1,
[data-bs-theme="dark"] h2,
[data-bs-theme="dark"] h3,
[data-bs-theme="dark"] h4,
[data-bs-theme="dark"] h5,
[data-bs-theme="dark"] h6 {
    color: #f3f4f6 !important;
}

/* Force headings inside text-white containers to stay white */
.text-white h1,
.text-white h2,
.text-white h3,
.text-white h4,
.text-white h5,
.text-white h6 {
    color: #ffffff !important;
}
</style>
<?= $headExtra ?? '' ?>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sb-brand">
        <div class="sb-icon"><i class="bi bi-buildings-fill text-white"></i></div>
        <div class="sb-text">
            <div class="n"><?= htmlspecialchars(DESA_NAMA ?? 'Desa') ?></div>
            <div class="s">Panel Admin</div>
        </div>
    </div>

    <!-- Admin badge -->
    <div class="sidebar-user d-flex align-items-center gap-2 mx-3 mt-3 mb-1 p-3 rounded-3" style="background:rgba(255,255,255,.07)">
        <?php if (!empty($user['foto_profil'])): ?>
            <img src="<?= APP_URL ?>/public/uploads/profil/<?= htmlspecialchars($user['foto_profil']) ?>" 
                 class="rounded-circle" 
                 style="width: 34px; height: 34px; object-fit: cover;" 
                 alt="Avatar">
        <?php else: ?>
            <div class="sb-badge"><i class="bi bi-person-gear"></i></div>
        <?php endif; ?>
        <div class="sidebar-user-info" style="overflow:hidden">
            <div class="text-white fw-semibold text-truncate" style="font-size:.78rem"><?= htmlspecialchars($user['nama'] ?? '') ?></div>
            <div class="text-white-50" style="font-size:.65rem">Admin Desa</div>
        </div>
    </div>

    <nav class="sb-nav">
        <?php
        $adminNav = [
            ['group' => 'Menu Utama', 'items' => [
                ['admin/dashboard',          'bi-speedometer2',      'Dashboard'],
            ]],
            ['group' => 'Data & Layanan', 'items' => [
                ['admin/penduduk',            'bi-people-fill',       'Kelola Penduduk'],
                ['admin/surat',               'bi-file-earmark-check','Kelola Surat'],
                ['admin/surat/templates',     'bi-file-earmark-text', 'Kelola Template'],
                ['admin/pengaduan',           'bi-megaphone-fill',    'Kelola Pengaduan'],
                ['admin/informasi',           'bi-newspaper',         'Kelola Informasi'],
            ]],
            ['group' => 'Sistem', 'items' => [
                ['admin/notifikasi',          'bi-bell-fill',         'Notifikasi'],
                ['admin/laporan',             'bi-bar-chart-fill',    'Laporan'],
                ['admin/settings',            'bi-gear-fill',         'Kelola Beranda'],
                ['admin/profil',              'bi-person-circle',     'Profil'],
            ]],
        ];
        $currentPath = ltrim(str_replace(parse_url(APP_URL, PHP_URL_PATH) ?? '', '', '/' . ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')), '/');
        foreach ($adminNav as $group):
        ?>
        <div class="section-label"><?= $group['group'] ?></div>
        <?php foreach ($group['items'] as [$path, $icon, $label]):
            if ($path === 'admin/surat') {
                $active = (str_starts_with($currentPath, 'admin/surat') && !str_starts_with($currentPath, 'admin/surat/template')) ? 'active' : '';
            } elseif ($path === 'admin/surat/templates') {
                $active = str_starts_with($currentPath, 'admin/surat/template') ? 'active' : '';
            } else {
                $active = str_starts_with($currentPath, $path) ? 'active' : '';
            }
        ?>
        <a href="<?= APP_URL ?>/<?= $path ?>" class="<?= $active ?>">
            <i class="bi <?= $icon ?>"></i>
            <span><?= $label ?></span>
        </a>
        <?php endforeach; endforeach; ?>
    </nav>

    <div class="sb-footer">
        <a href="<?= APP_URL ?>/auth/logout" onclick="return confirm('Yakin keluar?')">
            <i class="bi bi-box-arrow-left"></i><span>Keluar</span>
        </a>
    </div>
</aside>

<div class="main-wrap" id="mainWrap">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light p-1 d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <button class="btn btn-sm btn-light p-1 d-none d-lg-inline-flex" id="sidebarToggleDesktop">
                <i class="bi bi-layout-sidebar fs-5"></i>
            </button>
            <div>
                <div class="fw-bold" style="font-size:.95rem"><?= htmlspecialchars($pageTitle ?? 'Dashboard Admin') ?></div>
                <div class="text-muted" style="font-size:.7rem"><i class="bi bi-house me-1"></i><?= htmlspecialchars($pageTitle ?? '') ?></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-light" id="themeToggle" title="Ubah Tema" type="button">
                <i class="bi bi-moon-stars-fill" id="themeToggleIcon"></i>
            </button>
            <a href="<?= APP_URL ?>/admin/notifikasi" class="btn btn-sm btn-light">
                <i class="bi bi-bell"></i>
            </a>
            <div class="d-flex align-items-center gap-2 ps-2 border-start">
                <?php if (!empty($user['foto_profil'])): ?>
                    <img src="<?= APP_URL ?>/public/uploads/profil/<?= htmlspecialchars($user['foto_profil']) ?>" 
                         class="rounded-circle" 
                         style="width: 32px; height: 32px; object-fit: cover;" 
                         alt="Avatar">
                <?php else: ?>
                    <div class="bg-success rounded-3 text-white d-flex align-items-center justify-content-center fw-bold"
                         style="width:32px;height:32px;font-size:.7rem">
                        <?= strtoupper(substr($user['nama'] ?? 'A', 0, 2)) ?>
                    </div>
                <?php endif; ?>
                <div class="d-none d-sm-block">
                    <!-- TAMBAHAN: Ikon Pensil Edit Profil di Samping Nama Admin -->
                    <div class="fw-semibold d-flex align-items-center gap-1" style="font-size:.78rem;line-height:1.2">
                        <?= htmlspecialchars($user['nama'] ?? '') ?>
                        <i class="bi bi-pencil-square btn-trigger-edit-profil" title="Edit Akun Profil"></i>
                    </div>
                    <div class="text-muted" style="font-size:.65rem">Admin Desa</div>
                </div>
            </div>
        </div>
    </header>

    <main class="page-content">
        <?= $content ?>
    </main>

    <!-- ── MODAL EDIT PROFIL MODERN (GAYA WHATSAPP) ── -->
    <div class="modal fade" id="modalEditProfil" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: #0d1b2e;">
                        <i class="bi bi-person-badge text-success me-2"></i>Ubah Akun Profil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditProfil" autocomplete="off" enctype="multipart/form-data">
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted mb-0">Nama Lengkap</label>
                            <div class="wa-input-group">
                                <input type="text" id="inputNama" name="nama" class="form-control form-control-sm" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" required disabled>
                                <button type="button" class="wa-btn-pencil btn-toggle-field" data-target="#inputNama"><i class="bi bi-pencil-fill"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted mb-0">Username</label>
                            <div class="wa-input-group">
                                <input type="text" id="inputUsername" name="username" class="form-control form-control-sm" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required disabled>
                                <button type="button" class="wa-btn-pencil btn-toggle-field" data-target="#inputUsername"><i class="bi bi-pencil-fill"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted mb-0">Email</label>
                            <div class="wa-input-group">
                                <input type="email" id="inputEmail" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required disabled>
                                <button type="button" class="wa-btn-pencil btn-toggle-field" data-target="#inputEmail"><i class="bi bi-pencil-fill"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted mb-0">Foto Profil Baru (Opsional)</label>
                            <div class="input-group input-group-sm">
                                <input type="file" name="foto_profil" class="form-control form-control-sm" accept="image/*">
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.65rem;">Format: JPG, PNG, WEBP. Maks 5MB.</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold text-muted">Password Baru <span class="text-secondary font-monospace" style="font-size: .65rem;">(Kosongkan jika tidak diganti)</span></label>
                            <div class="input-group input-group-sm">
                                <input type="password" id="passwordBaru" name="password" class="form-control" placeholder="••••••••" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                        <button type="submit" class="btn btn-sm btn-success px-4" style="border-radius: 8px;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="app-footer d-flex justify-content-between">
        <span><?= htmlspecialchars(APP_FULL ?? '') ?> &copy; <?= date('Y') ?></span>
        <span class="font-monospace">Admin Panel &middot; v<?= htmlspecialchars(APP_VERSION ?? '1.0') ?></span>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
const APP_URL    = '<?= APP_URL ?>';
const CSRF_TOKEN = '<?= $csrfToken ?? '' ?>';
AOS.init({ once: true, duration: 400 });

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (window.innerWidth < 992) {
        sidebar.classList.toggle('mobile-open');
        overlay.style.display = sidebar.classList.contains('mobile-open') ? 'block' : 'none';
    } else {
        sidebar.classList.toggle('collapsed');
        document.getElementById('mainWrap').classList.toggle('collapsed');
        localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
    }
}

document.getElementById('sidebarToggleDesktop')?.addEventListener('click', toggleSidebar);

if (localStorage.getItem('sidebar_collapsed') === 'true') {
    document.getElementById('sidebar').classList.add('collapsed');
    document.getElementById('mainWrap').classList.add('collapsed');
}

setTimeout(() => {
    document.querySelectorAll('.alert-dismissible').forEach(el =>
        bootstrap.Alert.getOrCreateInstance(el).close()
    );
}, 4000);

// ── PROGRAM LOGIK INTERAKSI PENSIL EDIT PROFIL & AJAX ──
$(document).ready(function() {
    const editProfilModal = new bootstrap.Modal(document.getElementById('modalEditProfil'));

    // Pemicu Modal ketika Ikon Pensil/Tombol Ubah Akun diklik
    $(document).on('click', '.btn-trigger-edit-profil', function(e) {
        e.preventDefault();
        $('#formEditProfil input[name="nama"], #formEditProfil input[name="username"], #formEditProfil input[name="email"]').prop('disabled', true);
        $('.btn-toggle-field i').removeClass('bi-check-lg text-success').addClass('bi-pencil-fill');
        $('#passwordBaru').val('');
        editProfilModal.show();
    });

    // Toggle Input field gaya WA
    $('.btn-toggle-field').on('click', function() {
        const target = $($(this).data('target'));
        const icon = $(this).find('i');
        if (target.prop('disabled')) {
            target.prop('disabled', false).focus();
            icon.removeClass('bi-pencil-fill').addClass('bi-check-lg text-success');
        } else {
            target.prop('disabled', true);
            icon.removeClass('bi-check-lg text-success').addClass('bi-pencil-fill');
        }
    });

    // Show/Hide Password
    $('#togglePassword').on('click', function() {
        const passInput = $('#passwordBaru');
        const icon = $(this).find('i');
        const isPass = passInput.attr('type') === 'password';
        passInput.attr('type', isPass ? 'text' : 'password');
        icon.toggleClass('bi-eye bi-eye-slash');
    });

    // Submit Data Form via AJAX POST
    $('#formEditProfil').on('submit', function(e) {
        e.preventDefault();
        const disabledFields = $(this).find('input[name]:disabled');
        disabledFields.prop('disabled', false); // Aktifkan sementara agar data terbaca
        
        const formData = new FormData(this);
        disabledFields.prop('disabled', true); // Kembalikan kondisi awal UI
        formData.append('_csrf_token', CSRF_TOKEN);

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Data akun profil admin akan diperbarui.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Update!',
        }).then((result) => {
            if (!result.isConfirmed) return;
            Swal.showLoading();

            $.ajax({
                url: APP_URL + '/admin/profil',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.success || res.status === 'success') {
                        Swal.fire('Berhasil!', res.message || 'Profil berhasil diperbarui.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function(xhr) {
                    let errMsg = 'Server merespon dengan kesalahan internal. Pastikan isian form dan koneksi database valid.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', errMsg, 'error');
                }
            });
        });
    });

    // Logika Dark/Light Mode
    const themeToggleBtn = document.getElementById('themeToggle');
    const themeToggleIcon = document.getElementById('themeToggleIcon');

    function updateThemeUI(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        if (theme === 'dark') {
            if (themeToggleIcon) themeToggleIcon.className = 'bi bi-moon-stars-fill';
            if (themeToggleBtn) {
                themeToggleBtn.classList.remove('btn-light');
                themeToggleBtn.classList.add('btn-dark');
            }
        } else {
            if (themeToggleIcon) themeToggleIcon.className = 'bi bi-sun-fill';
            if (themeToggleBtn) {
                themeToggleBtn.classList.remove('btn-dark');
                themeToggleBtn.classList.add('btn-light');
            }
        }
    }

    const savedTheme = localStorage.getItem('theme') || 'dark';
    updateThemeUI(savedTheme);

    themeToggleBtn?.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'dark';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        updateThemeUI(newTheme);
    });
});

function confirmAction(url, title, text) {
    Swal.fire({ title, text, icon:'warning', showCancelButton:true,
        confirmButtonColor:'#1e4080', cancelButtonText:'Batal',
        confirmButtonText:'Ya, lanjutkan!' })
    .then(r => {
        if (!r.isConfirmed) return;
        $.post(url, { _csrf_token: CSRF_TOKEN }, res => {
            res.success
                ? Swal.fire('Berhasil!', res.message,'success').then(()=>location.reload())
                : Swal.fire('Gagal', res.message,'error');
        });
    });
}
</script>
<?= $footerExtra ?? '' ?>
</body>
</html>