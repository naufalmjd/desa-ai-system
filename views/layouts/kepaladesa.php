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
    <title><?= htmlspecialchars($pageTitle ?? 'Kepala Desa') ?> — <?= htmlspecialchars(APP_NAME ?? '') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      #d97706;
            --primary-dark: #b45309;
            --primary-glow: rgba(217, 119, 6, 0.15);
            --accent:       #fbbf24;
            --sidebar-bg:   #090d16;
            --sidebar-w:    260px; 
            --sidebar-c:    70px;  
            --header-h:     60px;
            --bg:           #0b0f19;
            --card-bg:      #111827;
            --border:       rgba(255, 255, 255, 0.06);
            --text-main:    #f3f4f6;
            --text-muted:   #9ca3af;
            --topbar-bg:    #111827;
        }
        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; transition: background 0.3s, color 0.3s; }

        /* --- STRUKTUR SIDEBAR PREMIUM --- */
        .sidebar { 
            width: var(--sidebar-w); 
            min-height: 100vh; 
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

        /* --- PERBAIKAN UTAMA: Sembunyikan Teks Keluar Saat Collapsed --- */
        .sidebar.collapsed { 
            width: var(--sidebar-c); 
        }
        .sidebar.collapsed .sb-text,
        .sidebar.collapsed .sb-user-info,
        .sidebar.collapsed .sb-nav span,
        .sidebar.collapsed .sb-footer span, /* Sembunyikan kata 'Keluar' */
        .sidebar.collapsed .section-label { 
            opacity: 0;
            pointer-events: none;
            display: none !important; 
        }
        .sidebar.collapsed .sb-brand,
        .sidebar.collapsed .sidebar-user {
            justify-content: center;
            padding: 1rem 0;
        }
        .sidebar.collapsed .sb-nav a,
        .sidebar.collapsed .sb-footer a { /* Posisikan ikon keluar pas di tengah */
            justify-content: center;
            padding: 0.75rem 0;
        }

        /* Bagian Brand / Logo */
        .sb-brand { 
            display: flex; 
            align-items: center; 
            gap: .75rem; 
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.05); 
            min-height: var(--header-h); 
        }
        .sb-icon { 
            width: 36px; 
            height: 36px; 
            background: linear-gradient(135deg, #f59e0b, #b45309); 
            border-radius: 10px;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
        }
        .sb-text { transition: opacity 0.2s ease; }
        .sb-text .n { color: #fff; font-weight: 700; font-size: .85rem; letter-spacing: 0.5px; }
        .sb-text .s { color: rgba(255,255,255,.4); font-size: .7rem; }

        /* Profil Informasi Kepala Desa di Sidebar */
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.25rem 1rem 0.5rem;
            padding: 0.75rem;
            border-radius: 12px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.06);
            transition: all 0.2s ease;
        }
        .sb-badge { 
            width: 36px; 
            height: 36px; 
            border-radius: 8px; 
            background: rgba(255,255,255,0.1);
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: #f59e0b;
            font-size: 1.1rem; 
            flex-shrink: 0; 
        }
        .sb-user-info { overflow: hidden; transition: opacity 0.2s ease; }

        /* Navigasi Menu */
        .sb-nav { flex: 1; overflow-y: auto; padding: 0.75rem; }
        .sb-nav .section-label { 
            color: rgba(255,255,255,.25); 
            font-size: .65rem; 
            font-weight: 700;
            text-transform: uppercase; 
            letter-spacing: .1em; 
            padding: 1.25rem .75rem .5rem; 
        }
        .sb-nav a { 
            display: flex; 
            align-items: center; 
            gap: .85rem; 
            color: rgba(255,255,255,.65);
            padding: .7rem .85rem; 
            border-radius: 10px; 
            text-decoration: none;
            font-size: .82rem; 
            font-weight: 500; 
            transition: all .2s ease; 
            margin-bottom: 2px;
        }
        .sb-nav a i { font-size: 1.1rem; flex-shrink: 0; width: 20px; text-align: center; }
        .sb-nav a:hover { background: rgba(255,255,255,.06); color: #fff; }

        /* Menu Navigasi Aktif */
        .sb-nav a.active { 
            background: linear-gradient(90deg, rgba(217,119,6,0.15) 0%, rgba(217,119,6,0.02) 100%); 
            color: #f59e0b; 
            font-weight: 600;
            position: relative;
        }
        .sb-nav a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            background: #f59e0b;
            border-radius: 0 4px 4px 0;
        }

        /* Footer Sidebar */
        .sb-footer { padding: 0.75rem; border-top: 1px solid rgba(255,255,255,.05); }
        .sb-footer a { 
            display: flex; 
            align-items: center; 
            gap: .85rem; 
            color: #ef4444;
            padding: .7rem .85rem; 
            border-radius: 10px; 
            font-size: .82rem;
            font-weight: 500; 
            text-decoration: none; 
            transition: .15s; 
        }
        .sb-footer a i { font-size: 1.1rem; width: 20px; text-align: center; }
        .sb-footer a:hover { background: rgba(239,68,68,.1); color: #f87171; }

        /* --- KONTEN UTAMA & TOPBAR --- */
        .main-wrap { 
            margin-left: var(--sidebar-w); 
            min-height: 100vh; 
            display: flex;
            flex-direction: column; 
            transition: margin-left .25s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .main-wrap.collapsed { margin-left: var(--sidebar-c); }

        .topbar { height: var(--header-h); background: var(--topbar-bg); border-bottom: 1px solid var(--border);
                  display: flex; align-items: center; justify-content: space-between;
                  padding: 0 1.25rem; position: sticky; top: 0; z-index: 100; }

        .page-content { flex: 1; padding: 1.5rem; }
        .card { 
            transition: transform .25s ease, box-shadow .25s ease;
            background: var(--card-bg) !important;
            color: var(--text-main);
            border: 1px solid var(--border) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
            border-radius: 14px;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(217, 119, 6, 0.15) !important;
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
                       padding: .75rem 1.25rem; font-size: .68rem; color: var(--text-muted); }

        /* Form Inputs in Dark Mode */
        .form-control, .form-select, .input-group-text {
            background-color: #1f2937 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #f3f4f6 !important;
            border-radius: 10px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.25) !important;
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
            .sidebar { transform: translateX(-100%); width: var(--sidebar-w) !important; }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-wrap { margin-left: 0 !important; }
            .sidebar-overlay { display: block !important; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1039; }

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

<aside class="sidebar" id="sidebar">
    <div class="sb-brand">
        <div class="sb-icon"><i class="bi bi-award-fill text-white"></i></div>
        <div class="sb-text">
            <div class="n"><?= htmlspecialchars(DESA_NAMA ?? 'Desa') ?></div>
            <div class="s">Kepala Desa Panel</div>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="sb-badge"><i class="bi bi-person-fill-lock"></i></div>
        <div class="sb-user-info">
            <div class="text-white fw-semibold text-truncate" style="font-size:.78rem"><?= htmlspecialchars($user['nama'] ?? '') ?></div>
            <div class="text-white-50" style="font-size:.65rem">Kepala Desa</div>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="section-label">Menu Utama</div>
        <a href="<?= APP_URL ?>/kepaladesa/dashboard" class="<?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i><span>Dashboard Eksekutif</span>
        </a>
        
        <div class="section-label">Persetujuan & Dokumen</div>
        <a href="<?= APP_URL ?>/kepaladesa/surat" class="<?= str_contains($_SERVER['REQUEST_URI'], '/surat') && !str_contains($_SERVER['REQUEST_URI'], '/arsip') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-check-fill"></i><span>Persetujuan Surat</span>
        </a>
        <a href="<?= APP_URL ?>/kepaladesa/surat/arsip" class="<?= str_contains($_SERVER['REQUEST_URI'], '/arsip') ? 'active' : '' ?>">
            <i class="bi bi-archive-fill"></i><span>Arsip Persetujuan</span>
        </a>

        <div class="section-label">Sistem AI & Analitik</div>
        <a href="<?= APP_URL ?>/kepaladesa/ai-analytics" class="<?= str_contains($_SERVER['REQUEST_URI'], 'ai-analytics') ? 'active' : '' ?>">
            <i class="bi bi-cpu-fill"></i><span>AI Analytics & Maps</span>
        </a>
        <a href="<?= APP_URL ?>/kepaladesa/profil" class="<?= str_contains($_SERVER['REQUEST_URI'], 'profil') ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i><span>Profil</span>
        </a>
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
                <div class="fw-bold" style="font-size:.95rem"><?= htmlspecialchars($pageTitle ?? 'Dashboard Eksekutif') ?></div>
                <div class="text-muted" style="font-size:.7rem"><i class="bi bi-house me-1"></i><?= htmlspecialchars($pageTitle ?? '') ?></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-light" id="themeToggle" title="Ubah Tema" type="button">
                <i class="bi bi-moon-stars-fill" id="themeToggleIcon"></i>
            </button>
            <div class="d-flex align-items-center gap-2 ps-2">
                <div class="rounded-3 text-white d-flex align-items-center justify-content-center fw-bold"
                     style="width:32px;height:32px;font-size:.7rem;background:linear-gradient(135deg, #f59e0b, #b45309)">
                    <?= strtoupper(substr(htmlspecialchars($user['nama'] ?? 'K'), 0, 2)) ?>
                </div>
                <div class="d-none d-sm-block">
                    <div class="fw-semibold" style="font-size:.78rem"><?= htmlspecialchars($user['nama'] ?? '') ?></div>
                    <div class="text-muted" style="font-size:.65rem">Kepala Desa</div>
                </div>
            </div>
        </div>
    </header>

    <main class="page-content">
        <?= $content ?>
    </main>

    <footer class="app-footer d-flex justify-content-between">
        <span><?= htmlspecialchars(APP_FULL ?? '') ?> &copy; <?= date('Y') ?></span>
        <span class="font-monospace">Executive Portal &middot; v<?= htmlspecialchars(APP_VERSION ?? '1.0') ?></span>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
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

function confirmAction(url, title, text) {
    Swal.fire({ title, text, icon:'warning', showCancelButton:true,
        confirmButtonColor:'#d97706', cancelButtonText:'Batal',
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

// Logika Dark/Light Mode
(function() {
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
})();
</script>
<?= $footerExtra ?? '' ?>
</body>
</html>