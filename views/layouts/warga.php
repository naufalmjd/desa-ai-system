<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Dashboard' ?> — <?= APP_NAME ?></title>
<!-- Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- AOS -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<script>
    (function() {
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', theme);
    })();
</script>
<style>
    :root, [data-bs-theme="light"] {
        --primary:      #1e4080;
        --primary-dark: #163566;
        --primary-glow: rgba(30, 64, 128, 0.15);
        --accent:       #059669;
        --sidebar-bg:   #0f2342;
        --sidebar-w:    240px;
        --header-h:     58px;
        --bg:           #eef2f7;
        --card-bg:      #ffffff;
        --border:       rgba(30, 64, 128, 0.1);
        --text-main:    #0d1b2e;
        --text-muted:   #5a6a82;
        --topbar-bg:    #ffffff;
    }

    [data-bs-theme="dark"] {
        --bg:           #080d16;
        --card-bg:      #111827;
        --border:       rgba(255, 255, 255, 0.08);
        --text-main:    #f3f4f6;
        --text-muted:   #9ca3af;
        --topbar-bg:    #111827;
        --primary:      #6366f1;
        --primary-dark: #4f46e5;
        --primary-glow: rgba(99, 102, 241, 0.2);
        --accent:       #38bdf8;
        --sidebar-bg:   #090d16;
    }

    * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
    body { background: var(--bg); color: var(--text-main); min-height: 100vh; transition: background 0.3s, color 0.3s; }

    /* ── Sidebar ── */
    .sidebar {
        width: var(--sidebar-w); min-height: 100vh;
        background: var(--sidebar-bg); position: fixed; top: 0; left: 0; z-index: 1040;
        display: flex; flex-direction: column; transition: width .25s ease, transform .25s ease;
        overflow: hidden;
        border-right: 1px solid var(--border);
    }
    .sidebar.collapsed { width: 64px; }
    .sidebar-brand {
        display: flex; align-items: center; gap: .75rem;
        padding: 1rem; border-bottom: 1px solid rgba(255,255,255,.05);
        min-height: var(--header-h); flex-shrink: 0;
    }
    .sidebar-brand-icon { width: 36px; height: 36px; background: rgba(255,255,255,.15);
                           border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .sidebar-brand-text { white-space: nowrap; overflow: hidden; }
    .sidebar-brand-text .name { color: #fff; font-weight: 700; font-size: .8rem; }
    .sidebar-brand-text .sub  { color: rgba(255,255,255,.5); font-size: .68rem; }

    .sidebar-user { margin: .75rem; padding: .75rem; border-radius: 12px;
                    background: rgba(255, 255, 255, .07); flex-shrink: 0; transition: padding .25s ease; }
    .sidebar-user .avatar {
        width: 36px; height: 36px; border-radius: 10px; background: var(--primary);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: .75rem; flex-shrink: 0;
    }

    .sidebar-nav { flex: 1; overflow-y: auto; padding: .5rem; transition: padding .25s ease; }
    .sidebar-nav::-webkit-scrollbar { width: 3px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 2px; }

    .nav-item { margin-bottom: 2px; }
    .nav-link {
        display: flex; align-items: center; gap: .75rem;
        color: rgba(255,255,255,.6); padding: .6rem .75rem; border-radius: 10px;
        text-decoration: none; font-size: .8rem; font-weight: 500; white-space: nowrap;
        transition: .15s; position: relative;
    }
    .nav-link:hover  { background: rgba(255,255,255,.08); color: #fff; }
    .nav-link.active { background: rgba(255,255,255,.15); color: #fff; }
    .nav-link.active::after {
        content: ''; position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
        width: 4px; height: 20px; background: var(--accent); border-radius: 2px;
    }
    .nav-link i { font-size: 1rem; flex-shrink: 0; }

    .sidebar-footer { padding: .75rem; border-top: 1px solid rgba(255,255,255,.05); flex-shrink: 0; transition: padding .25s ease; }
    .sidebar-footer .nav-link { color: #f87171; }
    .sidebar-footer .nav-link:hover { background: rgba(239,68,68,.15); }

    /* ── Perbaikan Collapsed Sidebar ── */
    .sidebar.collapsed .sidebar-brand-text,
    .sidebar.collapsed .sidebar-user .sidebar-brand-text,
    .sidebar.collapsed .nav-link span {
        display: none !important;
    }
    .sidebar.collapsed .sidebar-user { padding: .75rem 0; }
    .sidebar.collapsed .sidebar-user .d-flex { justify-content: center !important; gap: 0 !important; } 
    .sidebar.collapsed .sidebar-nav { padding: .5rem .25rem; }
    .sidebar.collapsed .sidebar-footer { padding: .75rem 0; }
    .sidebar.collapsed .nav-link { justify-content: center; padding: .6rem 0; gap: 0; }
    .sidebar.collapsed .nav-link i { width: 20px; text-align: center; } 
    .sidebar.collapsed .nav-link.active::after { right: 2px; }

    /* ── Main ── */
    .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; transition: margin-left .25s; }
    .main-wrap.collapsed { margin-left: 64px; }

    .topbar {
        height: var(--header-h); background: var(--topbar-bg); border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 1.25rem; position: sticky; top: 0; z-index: 100;
    }
    .topbar-title { font-weight: 700; font-size: .95rem; color: var(--text-main); }
    .topbar-breadcrumb { font-size: .7rem; color: var(--text-muted); }

    .page-content { flex: 1; padding: 1.5rem; }

    /* ── Cards & Tables ── */
    .card { background: var(--card-bg) !important; border: 1px solid var(--border) !important; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,.03) !important; transition: transform .25s, box-shadow .25s; color: var(--text-main); }
    .card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px var(--primary-glow) !important; }
    .card-header { background: rgba(255, 255, 255, 0.02) !important; border-bottom: 1px solid var(--border) !important; font-weight: 700; font-size: .85rem; color: var(--text-main); }
    .card-footer { background: rgba(255, 255, 255, 0.02) !important; border-top: 1px solid var(--border) !important; color: var(--text-muted); }
    .table { color: var(--text-main) !important; }
    .table th { font-size: .75rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); border-bottom: 2px solid var(--border) !important; background: transparent !important; }
    .table td { font-size: .8rem; vertical-align: middle; border-bottom: 1px solid var(--border) !important; background: transparent !important; color: var(--text-main); }
    .table-hover tbody tr:hover td { background-color: rgba(255, 255, 255, 0.02) !important; }

    /* Form Inputs */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .input-group-text {
        background-color: #1f2937 !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #f3f4f6 !important;
    }
    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px var(--primary-glow) !important;
        background-color: #1f2937 !important;
        color: #f3f4f6 !important;
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

    /* Modals */
    .modal-content {
        background-color: var(--card-bg);
        border: 1px solid var(--border);
        color: var(--text-main);
    }
    .modal-header, .modal-footer {
        border-color: var(--border);
    }
    [data-bs-theme="dark"] .modal-header .btn-close {
        filter: invert(1) !important;
    }

    /* Premium Elements */
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border: none; box-shadow: 0 4px 15px var(--primary-glow); color: #fff !important; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4); }
    .app-footer { background: var(--topbar-bg); border-top: 1px solid var(--border); padding: .5rem 1.25rem; font-size: .68rem; color: var(--text-muted); }

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
        border: none !important; border-bottom: 2px solid var(--accent) !important; border-radius: 0 !important;
        padding-left: 0; box-shadow: none !important; color: var(--text-main) !important;
    }
    [data-bs-theme="light"] .wa-input-group input:not(:disabled) {
        color: #1f2937 !important;
    }
    .wa-btn-pencil { background: none; border: none; color: var(--text-muted); padding: 4px 8px; transition: color 0.2s; }
    .wa-btn-pencil:hover { color: var(--accent); }
    .btn-trigger-edit-profil { cursor: pointer; transition: transform 0.2s; color: var(--accent); }
    .btn-trigger-edit-profil:hover { transform: scale(1.15); }

    /* ==========================================
       DARK MODE OVERRIDES (HIGH CONTRAST & READABLE)
       ========================================== */
    [data-bs-theme="dark"] body {
        background: var(--bg) !important;
        color: var(--text-main) !important;
    }
    [data-bs-theme="dark"] .bg-white {
        background-color: var(--card-bg) !important;
        color: var(--text-main) !important;
    }
    [data-bs-theme="dark"] .text-dark {
        color: var(--text-main) !important;
    }
    [data-bs-theme="dark"] .list-group-item {
        background-color: var(--card-bg) !important;
        color: var(--text-main) !important;
        border-color: var(--border) !important;
    }
    [data-bs-theme="dark"] .list-group-item-action:hover {
        background-color: rgba(255, 255, 255, 0.04) !important;
        color: #fff !important;
    }
    [data-bs-theme="dark"] .card {
        background: var(--card-bg) !important;
        border: 1px solid var(--border) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
        color: var(--text-main);
    }
    [data-bs-theme="dark"] .card:hover {
        box-shadow: 0 12px 30px var(--primary-glow) !important;
    }
    [data-bs-theme="dark"] .card-header {
        background: rgba(255, 255, 255, 0.02) !important;
        border-bottom: 1px solid var(--border) !important;
        color: var(--text-main) !important;
    }
    [data-bs-theme="dark"] .card-footer {
        background: rgba(255, 255, 255, 0.02) !important;
        border-top: 1px solid var(--border) !important;
        color: var(--text-muted) !important;
    }
    [data-bs-theme="dark"] .table {
        color: var(--text-main) !important;
    }
    [data-bs-theme="dark"] .table th {
        color: var(--text-muted) !important;
        border-bottom: 2px solid var(--border) !important;
        background: transparent !important;
    }
    [data-bs-theme="dark"] .table td {
        color: var(--text-main) !important;
        border-bottom: 1px solid var(--border) !important;
        background: transparent !important;
    }
    [data-bs-theme="dark"] .table-hover tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .input-group-text {
        background-color: #1f2937 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #f3f4f6 !important;
    }
    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px var(--primary-glow) !important;
    }
    [data-bs-theme="dark"] .modal-content {
        background-color: var(--card-bg) !important;
        border: 1px solid var(--border) !important;
        color: var(--text-main);
    }
    [data-bs-theme="dark"] .modal-header,
    [data-bs-theme="dark"] .modal-footer {
        border-color: var(--border) !important;
    }
    [data-bs-theme="dark"] .modal-header .btn-close {
        filter: invert(1) !important;
    }
    [data-bs-theme="dark"] .card h3 {
        color: var(--text-main) !important;
    }
    [data-bs-theme="dark"] .card small {
        color: var(--text-muted) !important;
    }
    [data-bs-theme="dark"] .card p.text-muted {
        color: var(--text-muted) !important;
    }
    [data-bs-theme="dark"] .topbar .btn-light {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border: none !important;
        color: #fff !important;
    }
    [data-bs-theme="dark"] .topbar .btn-light:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* Light Mode button standard style */
    [data-bs-theme="light"] .btn-light {
        background-color: #f3f4f6 !important;
        border-color: #e5e7eb !important;
        color: #1f2937 !important;
    }
    [data-bs-theme="light"] .btn-light:hover {
        background-color: #e5e7eb !important;
    }

    /* Chatbot Glassmorphic Adjustments for Dark Mode */
    [data-bs-theme="dark"] .card[style*="background: rgba(255, 255, 255, 0.7)"] {
        background: rgba(17, 28, 68, 0.7) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    [data-bs-theme="dark"] .faq-btn {
        background: rgba(30, 41, 59, 0.55) !important;
        color: #cbd5e1 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-bs-theme="dark"] .faq-btn:hover {
        background: #1e293b !important;
        color: #60a5fa !important;
    }
    [data-bs-theme="dark"] div[style*="background: #ffffff"] {
        background: var(--card-bg) !important;
        color: #f8fafc !important;
        border-color: var(--border) !important;
    }
    [data-bs-theme="dark"] div[style*="background: #white"] {
        background: var(--card-bg) !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] #chatInput:focus {
        background: #1e293b !important;
        color: #f8fafc !important;
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
</style>
<?= $headExtra ?? '' ?>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ── SIDEBAR ── -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-buildings-fill text-white"></i></div>
        <div class="sidebar-brand-text">
            <div class="name"><?= DESA_NAMA ?></div>
            <div class="sub"><?= DESA_KAB ?></div>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-2">
            <?php if (!empty($user['foto_profil'])): ?>
                <img src="<?= APP_URL ?>/public/uploads/profil/<?= htmlspecialchars($user['foto_profil']) ?>" 
                     class="rounded-circle" 
                     style="width: 36px; height: 36px; object-fit: cover;" 
                     alt="Avatar">
            <?php else: ?>
                <div class="avatar bg-primary"><?= strtoupper(substr($user['nama'] ?? 'U', 0, 2)) ?></div>
            <?php endif; ?>
            <div class="sidebar-brand-text">
                <div class="text-white fw-semibold" style="font-size:.78rem;line-height:1.2"><?= htmlspecialchars($user['nama'] ?? '') ?></div>
                <div class="text-white-50" style="font-size:.65rem">Warga</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php
        $wargaNav = [
            ['warga/dashboard',         'bi-speedometer2',   'Dashboard'],
            ['warga/surat/create',       'bi-file-earmark-plus', 'Pengajuan Surat'],
            ['warga/surat/tracking',     'bi-truck',          'Tracking Surat'],
            ['warga/pengaduan',          'bi-megaphone',      'Pengaduan'],
            ['warga/chatbot',            'bi-robot',          'AI Chatbot'],
            ['warga/informasi',          'bi-globe',          'Informasi Desa'],
            ['warga/notifikasi',         'bi-bell',           'Notifikasi'],
            ['warga/profil',             'bi-person-circle',  'Profil Saya'],
        ];
        $currentPath = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $currentPath = ltrim(str_replace(parse_url(APP_URL, PHP_URL_PATH) ?? '', '', '/' . $currentPath), '/');

        foreach ($wargaNav as [$path, $icon, $label]):
            $active = str_starts_with($currentPath, $path) ? 'active' : '';
        ?>
        <div class="nav-item">
            <a href="<?= APP_URL ?>/<?= $path ?>" class="nav-link <?= $active ?>">
                <i class="bi <?= $icon ?>"></i>
                <span><?= $label ?></span>
                <?php if ($label === 'Notifikasi' && ($notifCount ?? 0) > 0): ?>
                <span class="badge bg-danger ms-auto"><?= $notifCount ?></span>
                <?php endif; ?>
            </a>
        </div>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= APP_URL ?>/auth/logout" class="nav-link" onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-box-arrow-left"></i><span>Keluar</span>
        </a>
    </div>
</aside>

<!-- ── MAIN ── -->
<div class="main-wrap" id="mainWrap">
    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light p-1 d-lg-none" onclick="toggleSidebar()"><i class="bi bi-list fs-5"></i></button>
            <button class="btn btn-sm btn-light p-1 d-none d-lg-inline-flex" id="sidebarToggleDesktop"><i class="bi bi-layout-sidebar fs-5"></i></button>
            <div>
                <div class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></div>
                <div class="topbar-breadcrumb"><i class="bi bi-house me-1"></i><?= $pageTitle ?? 'Dashboard' ?></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-light" id="themeToggle" title="Ubah Tema" type="button">
                <i class="bi bi-moon-stars-fill" id="themeToggleIcon"></i>
            </button>
            <a href="<?= APP_URL ?>/warga/notifikasi" class="btn btn-sm btn-light position-relative">
                <i class="bi bi-bell"></i>
                <?php if (($notifCount ?? 0) > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem"><?= $notifCount ?></span>
                <?php endif; ?>
            </a>
            <div class="d-flex align-items-center gap-2 ps-2 border-start">
                <?php if (!empty($user['foto_profil'])): ?>
                    <img src="<?= APP_URL ?>/public/uploads/profil/<?= htmlspecialchars($user['foto_profil']) ?>" 
                         class="rounded-circle" 
                         style="width: 32px; height: 32px; object-fit: cover;" 
                         alt="Avatar">
                <?php else: ?>
                    <div class="bg-primary rounded-3 text-white d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:.7rem">
                        <?= strtoupper(substr($user['nama'] ?? 'U', 0, 2)) ?>
                    </div>
                <?php endif; ?>
                <div class="d-none d-md-block">
                    <!-- TAMBAHAN: Ikon Pensil Edit Profil di Samping Nama Warga -->
                    <div class="fw-semibold d-flex align-items-center gap-1" style="font-size:.78rem;line-height:1.2">
                        <?= htmlspecialchars($user['nama'] ?? '') ?>
                        <i class="bi bi-pencil-square btn-trigger-edit-profil" title="Edit Akun Profil"></i>
                    </div>
                    <div class="text-muted" style="font-size:.65rem;font-family:'JetBrains Mono',monospace"><?= htmlspecialchars($user['nik'] ?? '') ?></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="page-content">
        <?= $content ?>
    </main>

    <!-- ── MODAL EDIT PROFIL MODERN (GAYA WHATSAPP) ── -->
    <div class="modal fade" id="modalEditProfil" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem; color: #0d1b2e;">
                        <i class="bi bi-person-badge text-primary me-2"></i>Ubah Akun Profil
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
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted mb-0">NIK (Nomor Induk Kependudukan)</label>
                            <div class="wa-input-group" style="border-bottom-style: dashed;">
                                <input type="text" class="form-control form-control-sm font-monospace text-muted" value="<?= htmlspecialchars($user['nik'] ?? '-') ?>" disabled>
                                <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.65rem;">Terkunci</span>
                            </div>
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
                        <button type="submit" class="btn btn-sm btn-primary px-4" style="border-radius: 8px;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="app-footer d-flex justify-content-between align-items-center">
        <span><?= APP_FULL ?> &copy; <?= date('Y') ?> &mdash; v<?= APP_VERSION ?></span>
        <span class="d-flex gap-3">
            <span class="d-flex align-items-center gap-1"><span class="bg-success rounded-circle" style="width:6px;height:6px;display:inline-block"></span>AI Server Online</span>
            <span class="font-monospace">FastAPI &middot; YOLOv8 &middot; Gemini</span>
        </span>
    </footer>
</div>

<!-- ── Scripts ── -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>
const CSRF_TOKEN = '<?= $csrfToken ?? '' ?>';
const APP_URL    = '<?= APP_URL ?>';

AOS.init({ once: true, duration: 400 });

const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
if (isCollapsed && window.innerWidth >= 992) {
    document.getElementById('sidebar').classList.add('collapsed');
    document.getElementById('mainWrap').classList.add('collapsed');
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainWrap = document.getElementById('mainWrap');
    if (window.innerWidth < 992) {
        sidebar.classList.toggle('mobile-open');
        overlay.style.display = sidebar.classList.contains('mobile-open') ? 'block' : 'none';
    } else {
        sidebar.classList.toggle('collapsed');
        mainWrap.classList.toggle('collapsed');
        localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
    }
}
document.getElementById('sidebarToggleDesktop')?.addEventListener('click', toggleSidebar);

setTimeout(() => {
    document.querySelectorAll('.alert-dismissible').forEach(el => { bootstrap.Alert.getOrCreateInstance(el).close(); });
}, 4000);

$.fn.dataTable.defaults.language = { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' };

// ── PROGRAM LOGIK INTERAKSI PENSIL EDIT PROFIL & AJAX ──
$(document).ready(function() {
    const editProfilModal = new bootstrap.Modal(document.getElementById('modalEditProfil'));

    // Pemicu Modal ketika Ikon Pensil di Topbar diklik
    $('.btn-trigger-edit-profil').on('click', function(e) {
        e.preventDefault();
        $('#formEditProfil input[name="nama"], #formEditProfil input[name="username"], #formEditProfil input[name="email"]').prop('disabled', true);
        $('.btn-toggle-field i').removeClass('bi-check-lg text-success').addClass('bi-pencil-fill');
        $('#passwordBaru').val('');
        editProfilModal.show();
    });

    // Toggle Input field gaya WA (Buka gembok input saat pensil kecil diklik)
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
        
        // Buat FormData untuk mendukung file upload
        const formData = new FormData(this);
        disabledFields.prop('disabled', true); // Kembalikan kondisi awal UI
        formData.append('_csrf_token', CSRF_TOKEN);

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Data akun profil warga akan diperbarui.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e4080',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Update!',
        }).then((result) => {
            if (!result.isConfirmed) return;
            Swal.showLoading();

            $.ajax({
                url: APP_URL + '/warga/profil',
                method: 'POST',
                data: formData,
                processData: false, // Beritahu jQuery jangan memproses data
                contentType: false, // Beritahu jQuery jangan menetapkan contentType
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

    const savedTheme = localStorage.getItem('theme') || 'light';
    updateThemeUI(savedTheme);

    themeToggleBtn?.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        updateThemeUI(newTheme);
    });
});

function confirmAction(url, title, text, method = 'POST') {
    Swal.fire({
        title, text, icon: 'warning', showCancelButton: true, confirmButtonColor: '#1e4080', cancelButtonText: 'Batal', confirmButtonText: 'Ya, lanjutkan!',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url, method, data: { _csrf_token: CSRF_TOKEN },
            success: res => {
                if (res.success) { Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload()); }
                else { Swal.fire('Gagal', res.message, 'error'); }
            },
            error: () => Swal.fire('Error', 'Terjadi kesalahan.', 'error'),
        });
    });
}
</script>
<?= $footerExtra ?? '' ?>
</body>
</html>