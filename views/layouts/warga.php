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

<style>
:root {
    --primary:      #1e4080;
    --primary-dark: #163566;
    --accent:       #059669;
    --sidebar-bg:   #0f2342;
    --sidebar-w:    240px;
    --header-h:     58px;
    --bg:           #eef2f7;
    --card-bg:      #ffffff;
    --border:       rgba(30,64,128,.1);
}
* { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
body { background: var(--bg); min-height: 100vh; }

/* ── Sidebar ── */
.sidebar {
    width: var(--sidebar-w); min-height: 100vh;
    background: var(--sidebar-bg); position: fixed; top: 0; left: 0; z-index: 1040;
    display: flex; flex-direction: column; transition: width .25s ease;
    overflow: hidden;
}
.sidebar.collapsed { width: 64px; }
.sidebar-brand {
    display: flex; align-items: center; gap: .75rem;
    padding: 1rem; border-bottom: 1px solid rgba(255,255,255,.08);
    min-height: var(--header-h); flex-shrink: 0;
}
.sidebar-brand-icon { width: 36px; height: 36px; background: rgba(255,255,255,.15);
                       border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.sidebar-brand-text { white-space: nowrap; overflow: hidden; }
.sidebar-brand-text .name { color: #fff; font-weight: 700; font-size: .8rem; }
.sidebar-brand-text .sub  { color: rgba(255,255,255,.5); font-size: .68rem; }

.sidebar-user { margin: .75rem; padding: .75rem; border-radius: 12px;
                background: rgba(255,255,255,.07); flex-shrink: 0; }
.sidebar-user .avatar {
    width: 36px; height: 36px; border-radius: 10px; background: #1e4080;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: .75rem; flex-shrink: 0;
}

.sidebar-nav { flex: 1; overflow-y: auto; padding: .5rem; }
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
    width: 4px; height: 20px; background: #60a5fa; border-radius: 2px;
}
.nav-link i { font-size: 1rem; flex-shrink: 0; }

.sidebar-footer { padding: .75rem; border-top: 1px solid rgba(255,255,255,.08); flex-shrink: 0; }
.sidebar-footer .nav-link { color: #f87171; }
.sidebar-footer .nav-link:hover { background: rgba(239,68,68,.15); }

/* ── Main ── */
.main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; transition: margin-left .25s; }
.main-wrap.collapsed { margin-left: 64px; }

.topbar {
    height: var(--header-h); background: #fff; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 1.25rem; position: sticky; top: 0; z-index: 100;
}
.topbar-title { font-weight: 700; font-size: .95rem; color: #0d1b2e; }
.topbar-breadcrumb { font-size: .7rem; color: #5a6a82; }

.page-content { flex: 1; padding: 1.5rem; }

/* ── Cards ── */
.card { border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
.card-header { border-bottom: 1px solid var(--border); font-weight: 700; font-size: .85rem; }
.stat-card { border-radius: 14px; padding: 1rem; border: 1.5px solid; }

/* ── Badges & Status ── */
.badge-status { font-family: 'JetBrains Mono', monospace; font-size: .68rem; }

/* ── Table ── */
.table th { font-size: .75rem; font-weight: 600; text-transform: uppercase;
             letter-spacing: .03em; color: #5a6a82; border-bottom: 2px solid var(--border); }
.table td { font-size: .8rem; vertical-align: middle; }
.table-hover tbody tr:hover { background: rgba(30,64,128,.03); }

/* ── Footer ── */
.app-footer { background: #fff; border-top: 1px solid var(--border);
               padding: .5rem 1.25rem; font-size: .68rem; color: #5a6a82; }

/* Responsive */
@media (max-width: 991px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.mobile-open { transform: translateX(0); }
    .main-wrap { margin-left: 0 !important; }
    .sidebar-overlay { display: block !important; }
}
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1039; }
/* Premium Modernization */
.card {
    transition: transform .25s ease, box-shadow .25s ease;
    border: 1px solid rgba(30, 64, 128, 0.08) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(30, 64, 128, 0.06) !important;
}
.btn-primary {
    background: linear-gradient(135deg, #1e4080, #163566);
    border: none;
    box-shadow: 0 4px 15px rgba(30, 64, 128, 0.2);
    transition: all .25s ease;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(30, 64, 128, 0.3);
}
.sidebar-user {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02));
    border: 1px solid rgba(255, 255, 255, 0.06);
}
.sidebar-brand-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
}
.sidebar-brand-icon i {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}
</style>
<?= $headExtra ?? '' ?>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ── SIDEBAR ── -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-buildings-fill text-white"></i>
        </div>
        <div class="sidebar-brand-text">
            <div class="name"><?= DESA_NAMA ?></div>
            <div class="sub"><?= DESA_KAB ?></div>
        </div>
    </div>

    <!-- User Info -->
    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar bg-primary"><?= strtoupper(substr($user['nama'] ?? 'U', 0, 2)) ?></div>
            <div class="sidebar-brand-text">
                <div class="text-white fw-semibold" style="font-size:.78rem;line-height:1.2"><?= htmlspecialchars($user['nama'] ?? '') ?></div>
                <div class="text-white-50" style="font-size:.65rem">Warga</div>
            </div>
        </div>
    </div>

    <!-- Nav -->
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
        // strip base
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
        <a href="<?= APP_URL ?>/auth/logout" class="nav-link"
           onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-box-arrow-left"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

<!-- ── MAIN ── -->
<div class="main-wrap" id="mainWrap">
    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light p-1 d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <button class="btn btn-sm btn-light p-1 d-none d-lg-inline-flex" id="sidebarToggleDesktop">
                <i class="bi bi-layout-sidebar fs-5"></i>
            </button>
            <div>
                <div class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></div>
                <div class="topbar-breadcrumb">
                    <i class="bi bi-house me-1"></i><?= $pageTitle ?? 'Dashboard' ?>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= APP_URL ?>/warga/notifikasi" class="btn btn-sm btn-light position-relative">
                <i class="bi bi-bell"></i>
                <?php if (($notifCount ?? 0) > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                    <?= $notifCount ?>
                </span>
                <?php endif; ?>
            </a>
            <div class="d-flex align-items-center gap-2 ps-2 border-start">
                <div class="bg-primary rounded-3 text-white d-flex align-items-center justify-content-center fw-bold"
                     style="width:32px;height:32px;font-size:.7rem">
                    <?= strtoupper(substr($user['nama'] ?? 'U', 0, 2)) ?>
                </div>
                <div class="d-none d-md-block">
                    <div class="fw-semibold" style="font-size:.78rem;line-height:1.2"><?= htmlspecialchars($user['nama'] ?? '') ?></div>
                    <div class="text-muted" style="font-size:.65rem;font-family:'JetBrains Mono',monospace"><?= htmlspecialchars($user['nik'] ?? '') ?></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="page-content">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="app-footer d-flex justify-content-between align-items-center">
        <span><?= APP_FULL ?> &copy; <?= date('Y') ?> &mdash; v<?= APP_VERSION ?></span>
        <span class="d-flex gap-3">
            <span class="d-flex align-items-center gap-1">
                <span class="bg-success rounded-circle" style="width:6px;height:6px;display:inline-block"></span>
                AI Server Online
            </span>
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
// Global CSRF token untuk AJAX
const CSRF_TOKEN = '<?= $csrfToken ?? '' ?>';
const APP_URL    = '<?= APP_URL ?>';

AOS.init({ once: true, duration: 400 });

// ── Sidebar toggle ────────────────────────────────────────────────────────────
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

// Restore collapse state
if (localStorage.getItem('sidebar_collapsed') === 'true') {
    document.getElementById('sidebar').classList.add('collapsed');
    document.getElementById('mainWrap').classList.add('collapsed');
}

// ── Flash alert auto-dismiss ──────────────────────────────────────────────────
setTimeout(() => {
    document.querySelectorAll('.alert-dismissible').forEach(el => {
        bootstrap.Alert.getOrCreateInstance(el).close();
    });
}, 4000);

// ── DataTables default ────────────────────────────────────────────────────────
$.fn.dataTable.defaults.language = {
    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/id.json'
};

// ── SweetAlert2 AJAX confirm helper ──────────────────────────────────────────
function confirmAction(url, title, text, method = 'POST') {
    Swal.fire({
        title, text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1e4080',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, lanjutkan!',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url, method,
            data: { _csrf_token: CSRF_TOKEN },
            success: res => {
                if (res.success) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: () => Swal.fire('Error', 'Terjadi kesalahan.', 'error'),
        });
    });
}
</script>
<?= $footerExtra ?? '' ?>
</body>
</html>
