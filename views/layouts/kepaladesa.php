<?php
if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'superadmin') {
    require VIEW_PATH . '/layouts/superadmin.php';
    return;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Kepala Desa' ?> — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
    --primary:      #0f172a;
    --accent:       #b45309;
    --sidebar-bg:   #0f172a;
    --sidebar-w:    245px;
    --header-h:     58px;
    --bg:           #f1f5f9;
    --card-bg:      #ffffff;
    --border:       rgba(30,64,128,.1);
}
* { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
body { background: var(--bg); min-height: 100vh; }

.sidebar { width:var(--sidebar-w); min-height:100vh; background:var(--sidebar-bg);
           position:fixed; top:0; left:0; z-index:1040; display:flex;
           flex-direction:column; transition:all .25s ease; overflow:hidden; border-right: 1px solid rgba(255,255,255,.05); }
.sidebar.collapsed { width:64px; }

.sb-brand { display:flex; align-items:center; gap:.75rem; padding:1rem;
             border-bottom:1px solid rgba(255,255,255,.08); min-height:var(--header-h); }
.sb-icon { width:36px;height:36px;background:linear-gradient(135deg, #d97706, #b45309);border-radius:10px;
           display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.sb-text .n { color:#fff;font-weight:700;font-size:.8rem;white-space:nowrap; }
.sb-text .s { color:rgba(255,255,255,.5);font-size:.68rem;white-space:nowrap; }

.sb-badge { width:34px;height:34px;border-radius:10px;background:#d97706;
             display:flex;align-items:center;justify-content:center;color:#fff;
             font-weight:700;font-size:.7rem;flex-shrink:0; }

.sb-nav { flex:1;overflow-y:auto;padding:.5rem; }
.sb-nav a { display:flex;align-items:center;gap:.75rem;color:rgba(255,255,255,.6);
             padding:.6rem .75rem;border-radius:10px;text-decoration:none;
             font-size:.8rem;font-weight:500;white-space:nowrap;transition:.15s; }
.sb-nav a:hover { background:rgba(255,255,255,.08);color:#fff; }
.sb-nav a.active { background:rgba(255,255,255,.15);color:#fff; border-left: 3px solid #d97706; border-radius: 0 10px 10px 0; padding-left: calc(.75rem - 3px); }
.sb-nav a i { font-size:1rem;flex-shrink:0; }
.sb-nav .section-label { color:rgba(255,255,255,.3);font-size:.6rem;font-weight:700;
                          text-transform:uppercase;letter-spacing:.08em;padding:.75rem .75rem .3rem; }

.sb-footer { padding:.75rem;border-top:1px solid rgba(255,255,255,.08); }
.sb-footer a { display:flex;align-items:center;gap:.75rem;color:#f87171;
               padding:.6rem .75rem;border-radius:10px;font-size:.8rem;
               font-weight:500;text-decoration:none;transition:.15s; }
.sb-footer a:hover { background:rgba(239,68,68,.15); }

.main-wrap { margin-left:var(--sidebar-w);min-height:100vh;display:flex;
             flex-direction:column;transition:margin-left .25s ease; }
.main-wrap.collapsed { margin-left:64px; }

.topbar { height:var(--header-h);background:#fff;border-bottom:1px solid rgba(30,64,128,.1);
          display:flex;align-items:center;justify-content:space-between;
          padding:0 1.25rem;position:sticky;top:0;z-index:100; }

.page-content { flex:1;padding:1.5rem; }
.card { border:1px solid rgba(30,64,128,.1);border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,.04); }
.table th { font-size:.72rem;font-weight:700;text-transform:uppercase;
             letter-spacing:.04em;color:#5a6a82;border-bottom:2px solid rgba(30,64,128,.1); }
.table td { font-size:.8rem;vertical-align:middle; }
.app-footer { background:#fff;border-top:1px solid rgba(30,64,128,.1);
               padding:.5rem 1.25rem;font-size:.68rem;color:#5a6a82; }

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
.sb-icon {
    background: linear-gradient(135deg, #f59e0b, #b45309) !important;
    box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
}
.sb-icon i {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}
</style>
<?= $headExtra ?? '' ?>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sb-brand">
        <div class="sb-icon"><i class="bi bi-award-fill text-white"></i></div>
        <div class="sb-text">
            <div class="n"><?= DESA_NAMA ?></div>
            <div class="s">Kepala Desa Panel</div>
        </div>
    </div>

    <!-- Executive badge -->
    <div class="d-flex align-items-center gap-2 mx-3 mt-3 mb-1 p-3 rounded-3" style="background:rgba(255,255,255,.07)">
        <div class="sb-badge"><i class="bi bi-person-fill-lock"></i></div>
        <div style="overflow:hidden">
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
                <div class="fw-bold" style="font-size:.95rem"><?= $pageTitle ?? 'Dashboard Eksekutif' ?></div>
                <div class="text-muted" style="font-size:.7rem"><i class="bi bi-house me-1"></i><?= $pageTitle ?? '' ?></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center gap-2 ps-2">
                <div class="bg-warning rounded-3 text-white d-flex align-items-center justify-content-center fw-bold"
                     style="width:32px;height:32px;font-size:.7rem;background:linear-gradient(135deg, #d97706, #b45309) !important">
                    <?= strtoupper(substr($user['nama'] ?? 'K', 0, 2)) ?>
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
        <span><?= APP_FULL ?> &copy; <?= date('Y') ?></span>
        <span class="font-monospace">Executive Portal &middot; v<?= APP_VERSION ?></span>
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

// Restore collapse state
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
</script>
<?= $footerExtra ?? '' ?>
</body>
</html>
