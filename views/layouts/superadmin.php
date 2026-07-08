<?php
$user = $_SESSION['user'] ?? null;
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
<script>
    (function() {
        const theme = localStorage.getItem('sa_theme') || 'dark';
        document.documentElement.setAttribute('data-bs-theme', theme);
        if (theme === 'light') {
            document.documentElement.className = 'light-theme-init';
        }
    })();
</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Super Admin' ?> — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
    --primary:          #6366f1; /* Indigo */
    --primary-dark:     #4f46e5;
    --primary-glow:     rgba(99, 102, 241, 0.15);
    --accent:           #06b6d4; /* Cyan */
    --sidebar-bg:       #0b0f19;
    --sidebar-w:        250px;
    --header-h:         70px;
    --bg:               #0f172a; /* Slate 900 */
    --card-bg:          rgba(30, 41, 59, 0.7); /* Slate 800 with transparency */
    --card-border:      rgba(255, 255, 255, 0.05);
    --text-main:        #f1f5f9;
    --text-muted:       #94a3b8;
}

* { font-family: 'Outfit', sans-serif; box-sizing: border-box; }
body { background: var(--bg); min-height: 100vh; color: var(--text-main); overflow-x: hidden; }

/* Scrollbar customization */
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.3); }
::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.3); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: rgba(99, 102, 241, 0.5); }

/* Sidebar Premium Dark Glassmorphism */
.sidebar { width: var(--sidebar-w); height: 100vh; background: var(--sidebar-bg);
           position: fixed; top: 0; left: 0; z-index: 1040; display: flex;
           flex-direction: column; transition: all .3s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden;
           border-right: 1px solid rgba(255, 255, 255, 0.03); }
.sidebar.collapsed { width: 75px; }

.sb-brand { display: flex; align-items: center; gap: .75rem; padding: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03); min-height: var(--header-h); }
.sb-icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px;
           display: flex; align-items: center; justify-content: center; flex-shrink: 0;
           box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4); }
.sb-brand-text { transition: opacity 0.2s ease; }
.sb-brand-text .n { color: #fff; font-weight: 800; font-size: 1rem; letter-spacing: 0.5px; text-transform: uppercase; }
.sb-brand-text .s { color: var(--accent); font-size: .75rem; font-weight: 600; letter-spacing: 1px; }

.sidebar.collapsed .sb-brand-text { opacity: 0; width: 0; height: 0; overflow: hidden; }

.sb-badge { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(6, 182, 212, 0.2));
            display: flex; align-items: center; justify-content: center; color: var(--accent);
            font-weight: 700; font-size: .8rem; flex-shrink: 0; border: 1px solid rgba(6, 182, 212, 0.3); }

.sidebar-profile { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.03);
                   backdrop-filter: blur(10px); }
.sidebar.collapsed .sidebar-profile-text { display: none; }
.sidebar.collapsed .sidebar-profile { padding: 0.5rem !important; justify-content: center; }

.sb-nav { flex: 1; overflow-y: auto; padding: 1rem 0.75rem; }
.sb-nav a { display: flex; align-items: center; gap: .6rem; color: var(--text-muted);
            padding: .5rem .75rem; border-radius: 10px; text-decoration: none;
            font-size: .82rem; font-weight: 500; white-space: nowrap; transition: all 0.2s ease;
            margin-bottom: 0.15rem; }
.sb-nav a:hover { background: rgba(255, 255, 255, 0.03); color: #fff; }
.sb-nav a.active { background: linear-gradient(135deg, var(--primary), rgba(99, 102, 241, 0.6)); color: #fff;
                   box-shadow: 0 4px 15px var(--primary-glow); }
.sb-nav a i { font-size: 1.1rem; flex-shrink: 0; }
.sb-nav .section-label { color: rgba(255, 255, 255, 0.2); font-size: .62rem; font-weight: 800;
                          text-transform: uppercase; letter-spacing: .12em; padding: .6rem .75rem .25rem; }

.sidebar.collapsed .section-label { display: none; }
.sidebar.collapsed .sb-nav a span { display: none; }
.sidebar.collapsed .sb-nav a { justify-content: center; padding: .75rem; }

.sb-footer { padding: 1rem .75rem; border-top: 1px solid rgba(255, 255, 255, 0.03); }
.sb-footer a { display: flex; align-items: center; gap: .75rem; color: #f87171;
               padding: .75rem 1rem; border-radius: 12px; font-size: .88rem;
               font-weight: 500; text-decoration: none; transition: all 0.2s ease; }
.sb-footer a:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.sidebar.collapsed .sb-footer a { justify-content: center; padding: .75rem; }
.sidebar.collapsed .sb-footer a span { display: none; }

/* Main Wrapper & Topbar */
.main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex;
             flex-direction: column; transition: margin-left .3s cubic-bezier(0.4, 0, 0.2, 1); }
.main-wrap.collapsed { margin-left: 75px; }

.topbar { height: var(--header-h); background: rgba(11, 15, 25, 0.8); border-bottom: 1px solid rgba(255, 255, 255, 0.03);
          display: flex; align-items: center; justify-content: space-between;
          padding: 0 1.75rem; position: sticky; top: 0; z-index: 100; backdrop-filter: blur(15px); }

.page-content { flex: 1; padding: 2rem; }

/* Premium Card & Styling */
.card { background: var(--card-bg) !important; border: 1px solid var(--card-border) !important;
        border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37) !important;
        backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; }
.card:hover { transform: translateY(-4px); border-color: rgba(99, 102, 241, 0.2) !important;
              box-shadow: 0 12px 40px rgba(99, 102, 241, 0.1) !important; }

.card-header { background: rgba(255, 255, 255, 0.01) !important; border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
               color: #fff; font-weight: 600; }

.table { color: var(--text-main) !important; }
.table th { font-size: .75rem; font-weight: 700; text-transform: uppercase;
             letter-spacing: .06em; color: var(--text-muted); border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
             background: rgba(255, 255, 255, 0.01) !important; }
.table td { font-size: .85rem; vertical-align: middle; border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
             background: transparent !important; color: #e2e8f0; }
.table-hover tbody tr:hover td { background-color: rgba(255, 255, 255, 0.02) !important; }

/* Buttons & Inputs */
.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 600;
    border-radius: 10px;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
}

.form-control, .form-select {
    background-color: rgba(15, 23, 42, 0.6) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #fff !important;
    border-radius: 10px;
    padding: 0.6rem 1rem;
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}
.form-control:focus, .form-select:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
}
.form-control::placeholder {
    color: rgba(255,255,255,0.5) !important;
}
.dropdown-item {
    color: var(--text-main) !important;
    transition: background-color 0.2s, color 0.2s;
}
.dropdown-item:hover, .dropdown-item:focus {
    background-color: rgba(255, 255, 255, 0.05) !important;
    color: #fff !important;
}

.badge-role {
    font-weight: 600;
    font-size: 0.72rem;
    padding: 0.3rem 0.6rem;
    border-radius: 8px;
    border: 1px solid transparent;
}
.badge-role.warga { background: rgba(6, 182, 212, 0.1) !important; color: #22d3ee !important; border-color: rgba(6, 182, 212, 0.2); }
.badge-role.admin { background: rgba(245, 158, 11, 0.1) !important; color: #fbbf24 !important; border-color: rgba(245, 158, 11, 0.2); }
.badge-role.kepala_desa { background: rgba(16, 185, 129, 0.1) !important; color: #34d399 !important; border-color: rgba(16, 185, 129, 0.2); }
.badge-role.superadmin { background: rgba(139, 92, 246, 0.1) !important; color: #a78bfa !important; border-color: rgba(139, 92, 246, 0.2); }

.app-footer { background: rgba(11, 15, 25, 0.8); border-top: 1px solid rgba(255, 255, 255, 0.03);
               padding: 1rem 1.75rem; font-size: .75rem; color: var(--text-muted); backdrop-filter: blur(10px); }

@media (max-width: 991px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.mobile-open { transform: translateX(0); }
    .main-wrap { margin-left: 0 !important; }
    .sidebar-overlay { display: block !important; }
}
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1039; backdrop-filter: blur(4px); }

.text-gradient {
    background: linear-gradient(135deg, #fff 30%, var(--text-muted));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Light Theme Styling Overrides */
.light-theme-init body { background: #f1f5f9 !important; color: #0f172a !important; }

body.light-theme {
    --bg:               #f1f5f9; /* Slate 100 */
    --card-bg:          rgba(255, 255, 255, 0.75); /* White glassmorphism */
    --card-border:      rgba(0, 0, 0, 0.05);
    --text-main:        #0f172a; /* Slate 900 */
    --text-muted:       #475569; /* Slate 600 */
    --sidebar-bg:       #ffffff;
}
body.light-theme .sidebar {
    border-right: 1px solid rgba(0, 0, 0, 0.05);
}
body.light-theme .sb-brand {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}
body.light-theme .sidebar-profile {
    background: rgba(0, 0, 0, 0.02);
    border: 1px solid rgba(0, 0, 0, 0.03);
}
body.light-theme .sidebar-profile-text .text-white {
    color: #0f172a !important;
}
body.light-theme .sb-brand-text .n {
    color: #0f172a !important;
}
body.light-theme .sb-brand-text .s {
    color: var(--primary);
}
body.light-theme .sb-nav a:hover {
    background: rgba(0, 0, 0, 0.03);
    color: #0f172a;
}
body.light-theme .sb-nav a.active {
    color: #fff;
}
body.light-theme .sb-nav .section-label {
    color: rgba(0, 0, 0, 0.2);
}
body.light-theme .topbar {
    background: rgba(255, 255, 255, 0.8);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}
body.light-theme .topbar .text-white {
    color: #0f172a !important;
}
body.light-theme .topbar .border-secondary {
    border-color: rgba(0, 0, 0, 0.08) !important;
}
body.light-theme .topbar .btn-outline-secondary {
    border-color: rgba(0, 0, 0, 0.1);
}
body.light-theme .topbar .bi-list, body.light-theme .topbar .bi-layout-sidebar {
    color: #0f172a !important;
}
body.light-theme .app-footer {
    background: rgba(255, 255, 255, 0.8);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}
body.light-theme .table th {
    border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
    background: rgba(0, 0, 0, 0.01) !important;
    color: #475569;
}
body.light-theme .table td {
    border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
    color: #334155;
}
body.light-theme .table-hover tbody tr:hover td {
    background-color: rgba(0, 0, 0, 0.015) !important;
}
body.light-theme .form-control, body.light-theme .form-select {
    background-color: rgba(255, 255, 255, 0.8) !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    color: #000 !important;
}
body.light-theme .form-control::placeholder {
    color: rgba(0,0,0,0.4) !important;
}
body.light-theme .text-gradient {
    background: linear-gradient(135deg, #0f172a 30%, var(--text-muted)) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
}

body.light-theme .text-white-50 {
    color: #475569 !important;
}
body.light-theme .badge-role.warga { color: #0891b2 !important; border-color: rgba(8, 145, 178, 0.2) !important; background: rgba(8, 145, 178, 0.08) !important; }
body.light-theme .badge-role.admin { color: #ca8a04 !important; border-color: rgba(202, 138, 4, 0.2) !important; background: rgba(202, 138, 4, 0.08) !important; }
body.light-theme .badge-role.kepala_desa { color: #059669 !important; border-color: rgba(5, 150, 105, 0.2) !important; background: rgba(5, 150, 105, 0.08) !important; }
body.light-theme .badge-role.superadmin { color: #7c3aed !important; border-color: rgba(124, 58, 237, 0.2) !important; background: rgba(124, 58, 237, 0.08) !important; }

/* General Light Theme Badge text fallback */
body.light-theme .badge:not([class*="text-"]):not([class*="bg-"]) {
    color: #1f2937 !important;
}
body.light-theme .card-header {
    color: #0f172a !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
}
body.light-theme .btn-outline-light {
    color: #0f172a !important;
    border-color: rgba(0, 0, 0, 0.15) !important;
}
body.light-theme .btn-outline-light:hover {
    background-color: rgba(0, 0, 0, 0.05) !important;
    color: #0f172a !important;
}

.dropdown-item {
    color: var(--text-main) !important;
    transition: all 0.2s ease;
}
.dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
    color: #fff !important;
}
body.light-theme .dropdown-item:hover {
    background-color: rgba(0, 0, 0, 0.05) !important;
    color: #000 !important;
}
.dropdown-divider {
    border-color: var(--card-border) !important;
}

/* Chat bubble adaptations */
.bubble-user {
    background: rgba(255, 255, 255, 0.05) !important;
    color: var(--text-main) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}
.bubble-ai {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(6, 182, 212, 0.15)) !important;
    color: var(--text-main) !important;
    border: 1px solid rgba(99, 102, 241, 0.2) !important;
}
body.light-theme .bubble-user {
    background: rgba(0, 0, 0, 0.03) !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}
body.light-theme .bubble-ai {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(6, 182, 212, 0.1)) !important;
    border: 1px solid rgba(99, 102, 241, 0.15) !important;
}

/* Select dropdown options visibility */
select option {
    background-color: #0f172a !important;
    color: #fff !important;
}
body.light-theme select option {
    background-color: #ffffff !important;
    color: #0f172a !important;
}

/* Pagination active item overrides */
.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px var(--primary-glow);
}
.pagination .page-link:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
}
body.light-theme .pagination .page-link:hover {
    background: rgba(0, 0, 0, 0.05) !important;
    color: #0f172a !important;
}

/* Fix text-muted contrast on dark background */
body:not(.light-theme) .text-muted, 
body:not(.light-theme) .text-white-50 {
    color: #94a3b8 !important;
}
body:not(.light-theme) .input-group-text.text-muted {
    color: #94a3b8 !important;
}

/* Fix visibility of text-dark, bg-white, bg-light, text-black in dark mode */
body:not(.light-theme) .text-dark,
body:not(.light-theme) .text-black,
body:not(.light-theme) strong.text-dark,
body:not(.light-theme) span.text-dark,
body:not(.light-theme) div.text-dark,
body:not(.light-theme) td.text-dark,
body:not(.light-theme) h1.text-dark,
body:not(.light-theme) h2.text-dark,
body:not(.light-theme) h3.text-dark,
body:not(.light-theme) h4.text-dark,
body:not(.light-theme) h5.text-dark,
body:not(.light-theme) h6.text-dark,
body:not(.light-theme) .modal-title,
body:not(.light-theme) .modal-content h5,
body:not(.light-theme) .modal-content h6,
body:not(.light-theme) .card-title,
body:not(.light-theme) h1,
body:not(.light-theme) h2,
body:not(.light-theme) h3,
body:not(.light-theme) h4,
body:not(.light-theme) h5,
body:not(.light-theme) h6,
body:not(.light-theme) .badge.text-dark {
    color: #f3f4f6 !important;
}
body:not(.light-theme) .bg-white,
body:not(.light-theme) .card-header.bg-white,
body:not(.light-theme) .card-footer.bg-white {
    background-color: var(--card-bg) !important;
    border-color: var(--card-border) !important;
    color: #f3f4f6 !important;
}
body:not(.light-theme) .bg-light,
body:not(.light-theme) .badge.bg-light,
body:not(.light-theme) .bg-light.text-dark {
    background-color: rgba(255, 255, 255, 0.03) !important;
    color: #cbd5e1 !important;
    border-color: rgba(255, 255, 255, 0.06) !important;
}
body:not(.light-theme) .border {
    border-color: rgba(255, 255, 255, 0.06) !important;
}

/* Purple Status Badge Styling */
.bg-purple-subtle { background-color: rgba(139, 92, 246, 0.1) !important; }
.text-purple { color: #7c3aed !important; }
.border-purple-subtle { border-color: rgba(139, 92, 246, 0.2) !important; }

body:not(.light-theme) .bg-purple-subtle { background-color: rgba(139, 92, 246, 0.18) !important; }
body:not(.light-theme) .text-purple { color: #a78bfa !important; }
body:not(.light-theme) .border-purple-subtle { border-color: rgba(139, 92, 246, 0.3) !important; }

body.light-theme .badge-status.bg-secondary-subtle {
    color: #4b5563 !important;
    background-color: #f3f4f6 !important;
    border-color: #e5e7eb !important;
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
<script>
    if (localStorage.getItem('sa_theme') === 'light') {
        document.body.classList.add('light-theme');
    }
</script>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sb-brand">
        <div class="sb-icon"><i class="bi bi-shield-lock-fill fs-5" style="color: #fff !important;"></i></div>
        <div class="sb-brand-text">
            <div class="n"><?= DESA_NAMA ?></div>
            <div class="s">SUPER ADMIN</div>
        </div>
    </div>

    <!-- User profile card inside sidebar -->
    <div class="sidebar-profile d-flex align-items-center gap-2 mx-3 mt-2 mb-1 p-2 rounded-3">
        <div class="sb-badge"><i class="bi bi-person-workspace"></i></div>
        <div class="sidebar-profile-text" style="overflow:hidden">
            <div class="text-white fw-bold text-truncate" style="font-size:.8rem"><?= htmlspecialchars($user['nama'] ?? 'Super Admin') ?></div>
            <div class="text-accent" style="font-size:.68rem; font-weight: 600; color: var(--accent);">Developer Mode</div>
        </div>
    </div>

    <nav class="sb-nav">
        <?php
        $navGroups = [
            'Sistem & Keamanan' => [
                ['superadmin/dashboard', 'bi-grid-1x2-fill', 'Dashboard SA'],
                ['superadmin/user',      'bi-people-fill',    'Kelola Akun'],
                ['superadmin/log',       'bi-database-fill-gear', 'Log Audit'],
                ['superadmin/settings',  'bi-sliders2',       'Konfigurasi Web'],
                ['superadmin/backup',    'bi-database-fill-up','Backup Database'],
            ],
            'Pelayanan & Data' => [
                ['admin/penduduk',       'bi-people-fill',    'Data Penduduk'],
                ['admin/surat',          'bi-file-earmark-check','Kelola Surat'],
                ['admin/surat/templates','bi-file-earmark-text', 'Template Surat'],
                ['kepaladesa/surat',     'bi-check-all',      'Persetujuan Kades'],
                ['admin/pengaduan',      'bi-megaphone-fill',    'Aduan Masyarakat'],
                ['admin/informasi',      'bi-newspaper',         'Berita & Galeri'],
            ],
            'AI & Laporan' => [
                ['kepaladesa/ai-analytics','bi-cpu-fill',      'Analisis AI'],
                ['superadmin/chatbot',   'bi-robot',             'Log Chatbot AI'],
                ['admin/laporan',        'bi-bar-chart-fill',    'Laporan & Ekspor'],
                ['admin/notifikasi',     'bi-bell-fill',         'Notifikasi'],
            ],
            'Super Admin' => [
                ['superadmin/profil',    'bi-gear-fill',      'Profil Pengaturan'],
            ]
        ];
        
        $currentPath = ltrim(str_replace(parse_url(APP_URL, PHP_URL_PATH) ?? '', '', '/' . ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')), '/');
        
        foreach ($navGroups as $groupLabel => $items):
        ?>
        <div class="section-label"><?= $groupLabel ?></div>
        <?php foreach ($items as [$path, $icon, $label]):
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
        <a href="<?= APP_URL ?>/auth/logout" onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-box-arrow-left"></i><span>Log Out</span>
        </a>
    </div>
</aside>

<div class="main-wrap" id="mainWrap">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary p-1 d-lg-none" onclick="toggleSidebar()" style="border-color: rgba(255,255,255,0.1)">
                <i class="bi bi-list fs-5 text-white"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary p-1 d-none d-lg-inline-flex" id="sidebarToggleDesktop" style="border-color: rgba(255,255,255,0.1)">
                <i class="bi bi-layout-sidebar fs-5 text-white"></i>
            </button>
            <div>
                <div class="fw-bold text-gradient" style="font-size:1.05rem"><?= $pageTitle ?? 'Dashboard Super Admin' ?></div>
                <div class="text-muted" style="font-size:.72rem"><i class="bi bi-house me-1"></i>System Control / <?= $pageTitle ?? '' ?></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Theme Toggle Button -->
            <button class="btn btn-sm btn-outline-secondary p-1 border-0" id="themeToggleBtn" style="border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px;" title="Ganti Mode Tema">
                <i class="bi bi-moon-stars-fill fs-5 text-white" id="themeToggleIcon"></i>
            </button>
            
            <!-- Direct Logout Button in Topbar -->
            <a href="<?= APP_URL ?>/auth/logout" class="btn btn-sm btn-outline-danger p-1 border-0" style="border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px;" title="Keluar Sistem (Log Out)" onclick="return confirm('Yakin ingin keluar dari sistem?')">
                <i class="bi bi-box-arrow-left fs-5"></i>
            </a>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 ps-3 border-start border-secondary text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                    <div class="rounded-3 text-white d-flex align-items-center justify-content-center fw-bold"
                         style="width:36px;height:36px;font-size:.75rem; background: linear-gradient(135deg, var(--primary), var(--accent)); color: #fff !important;">
                        SA
                    </div>
                    <div class="d-none d-sm-block text-start">
                        <div class="fw-semibold text-white d-flex align-items-center gap-1" style="font-size:.82rem">
                            <?= htmlspecialchars($user['nama'] ?? 'Super Admin') ?>
                            <i class="bi bi-chevron-down text-white-50" style="font-size: 0.65rem;"></i>
                        </div>
                        <div class="text-accent" style="font-size:.7rem; font-weight: 600; color: var(--accent);">Super Admin</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 p-2" style="background: var(--card-bg); backdrop-filter: blur(15px); border: 1px solid var(--card-border) !important; min-width: 180px; border-radius: 12px;">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-3" href="<?= APP_URL ?>/superadmin/profil">
                            <i class="bi bi-person-fill text-primary"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-3" href="<?= APP_URL ?>/superadmin/settings">
                            <i class="bi bi-sliders text-accent"></i>
                            <span>Konfigurasi Web</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-3 text-danger" href="<?= APP_URL ?>/auth/logout" onclick="return confirm('Yakin ingin keluar?')">
                            <i class="bi bi-box-arrow-left"></i>
                            <span>Log Out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main class="page-content">
        <?= $content ?>
    </main>

    <footer class="app-footer d-flex justify-content-between">
        <span><?= APP_FULL ?> &copy; <?= date('Y') ?></span>
        <span class="font-monospace text-accent">Super Admin Console &middot; v<?= APP_VERSION ?></span>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        localStorage.setItem('sa_sidebar_collapsed', sidebar.classList.contains('collapsed'));
    }
}

document.getElementById('sidebarToggleDesktop')?.addEventListener('click', toggleSidebar);

if (localStorage.getItem('sa_sidebar_collapsed') === 'true') {
    document.getElementById('sidebar').classList.add('collapsed');
    document.getElementById('mainWrap').classList.add('collapsed');
}

setTimeout(() => {
    document.querySelectorAll('.alert-dismissible').forEach(el =>
        bootstrap.Alert.getOrCreateInstance(el).close()
    );
}, 5000);

// Theme Toggle Script
$(document).ready(function() {
    const btn = $('#themeToggleBtn');
    const icon = $('#themeToggleIcon');
    const body = $('body');

    // Set initial icon based on theme
    const currentTheme = localStorage.getItem('sa_theme') || 'dark';
    if (currentTheme === 'light') {
        icon.removeClass('bi-moon-stars-fill text-white').addClass('bi-sun-fill text-warning');
    }

    btn.on('click', function() {
        if (body.hasClass('light-theme')) {
            body.removeClass('light-theme');
            icon.removeClass('bi-sun-fill text-warning').addClass('bi-moon-stars-fill text-white');
            localStorage.setItem('sa_theme', 'dark');
            document.documentElement.className = '';
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        } else {
            body.addClass('light-theme');
            icon.removeClass('bi-moon-stars-fill text-white').addClass('bi-sun-fill text-warning');
            localStorage.setItem('sa_theme', 'light');
            document.documentElement.className = 'light-theme-init';
            document.documentElement.setAttribute('data-bs-theme', 'light');
        }
    });
});
</script>
<?= $footerExtra ?? '' ?>
</body>
</html>
