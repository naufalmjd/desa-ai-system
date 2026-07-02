<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — <?= APP_FULL ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #0f172a;
    --primary-light: #1e293b;
    --accent-emerald: #10b981;
    --accent-blue: #3b82f6;
    --body-bg: #f8fafc;
}
* { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
h1, h2, h3, .brand-title { font-family: 'Outfit', sans-serif; }
body { background: var(--body-bg); min-height: 100vh; display: flex; align-items: stretch; overflow-x: hidden; }

/* Left Panel - Hero section */
.login-left {
    background: linear-gradient(135deg, #0b0f19 0%, #1e293b 50%, #0f172a 100%);
    position: relative; overflow: hidden;
    display: flex; flex-direction: column; justify-content: space-between;
}
/* Glowing background blobs */
.login-left::before {
    content: ''; position: absolute; top: -10%; left: -10%; width: 50%; height: 50%;
    background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(0,0,0,0) 70%);
    filter: blur(50px); pointer-events: none;
}
.login-left::after {
    content: ''; position: absolute; bottom: -10%; right: -10%; width: 50%; height: 50%;
    background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, rgba(0,0,0,0) 70%);
    filter: blur(50px); pointer-events: none;
}

.stat-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 1.25rem;
    text-align: center;
    transition: transform 0.3s ease, border-color 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 255, 255, 0.15);
}

/* Form Inputs styling */
.form-label { color: #475569; font-weight: 600; }
.input-group { border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02); transition: all 0.25s ease; }
.input-group-text { background: #f8fafc; border: 1.5px solid #e2e8f0; color: #64748b; padding: 0.75rem 1rem; transition: all 0.25s ease; }
.form-control {
    border: 1.5px solid #e2e8f0; background: #fff;
    padding: 0.75rem 1rem; font-size: 0.875rem;
    transition: all 0.25s ease;
}
.form-control:focus {
    box-shadow: none !important;
}
.input-group:focus-within {
    box-shadow: 0 0 0 3.5px rgba(59,130,246,0.12) !important;
}
.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control,
.input-group:focus-within #togglePass {
    border-color: var(--accent-blue) !important;
}
.input-group .input-group-text:first-child {
    border-right: none;
}
.input-group .form-control:not(:first-child):not(:last-child) {
    border-left: none;
    border-right: none;
}
.input-group .form-control:last-child {
    border-left: none;
}
.input-group #togglePass {
    border: 1.5px solid #e2e8f0;
    border-left: none;
    background: #fff;
    color: #64748b;
    padding: 0.75rem 1rem;
    transition: all 0.25s ease;
}

.btn-submit {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #fff; border: none;
    border-radius: 12px; padding: 0.8rem 1.5rem;
    font-weight: 700; font-size: 0.9rem; transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
}
.btn-submit:hover {
    transform: translateY(-1.5px);
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.25);
    background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
}

.demo-btn {
    border-radius: 10px; font-size: 0.72rem; font-weight: 700; transition: all 0.2s ease;
    border: 1.5px solid; text-transform: uppercase; letter-spacing: 0.03em;
}
.demo-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Animations */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.animated-element {
    animation: fadeInUp 0.6s ease forwards;
}
</style>
</head>
<body>

<!-- Left Panel -->
<div class="d-none d-lg-flex flex-column justify-content-between login-left col-lg-6 p-5">
    <div class="position-relative z-1 animated-element" style="animation-delay: 0.1s">
        <div class="d-flex align-items-center gap-3 mb-5">
            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-10">
                <i class="bi bi-buildings-fill text-white fs-4"></i>
            </div>
            <div>
                <div class="text-white fw-bold brand-title fs-5"><?= DESA_NAMA ?></div>
                <small class="text-white-50 opacity-75"><?= DESA_KAB ?>, <?= DESA_PROV ?></small>
            </div>
        </div>
        <h1 class="text-white fw-black fs-1 lh-sm mb-3" style="letter-spacing: -0.02em">
            Sistem Pelayanan<br>
            <span class="text-info">Administrasi Desa</span><br>
            Berbasis <span class="text-success">Artificial Intelligence</span>
        </h1>
        <p class="text-white-50 mb-5" style="font-size:0.95rem; max-width: 520px; line-height: 1.6">
            Platform tata kelola digital terpadu untuk pelayanan administrasi warga secara cepat,
            pengaduan berbasis YOLOv8 Computer Vision, AI Chatbot Gemini,
            serta analitik eksekutif penunjang keputusan.
        </p>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach(['YOLOv8 Object Detection', 'Google Gemini AI', 'Tanda Tangan QR Code', 'Sistem Fast-Route'] as $badge): ?>
            <span class="badge px-3 py-2" style="background: rgba(255, 255, 255, 0.08) !important; color: rgba(255, 255, 255, 0.75) !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; font-size: 0.72rem; border-radius: 8px; font-weight: 500;">
                <?= $badge ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Statistics widgets -->
    <div class="row g-3 position-relative z-1 animated-element" style="animation-delay: 0.3s">
        <?php foreach([
            ['bi-people-fill','3.842','Penduduk Terdaftar','text-info'],
            ['bi-file-earmark-text-fill','128','Surat Diproses','text-warning'],
            ['bi-shield-fill-check','95%','Solusi Aduan','text-success'],
        ] as [$icon,$val,$label,$colorClass]): ?>
        <div class="col-4">
            <div class="stat-card">
                <i class="bi <?= $icon ?> <?= $colorClass ?> fs-4 mb-2 d-block"></i>
                <div class="text-white fw-bold fs-4"><?= $val ?></div>
                <small class="text-white-50" style="font-size: 0.72rem"><?= $label ?></small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Right Panel -->
<div class="col-12 col-lg-6 d-flex align-items-center justify-content-center p-4 p-lg-5">
    <div class="w-100 animated-element" style="max-width:400px; animation-delay: 0.2s">
        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-10 rounded-4 d-inline-flex p-3 mb-3 text-primary" style="background: rgba(59,130,246,0.08) !important">
                <i class="bi bi-shield-lock-fill fs-3 text-primary"></i>
            </div>
            <h2 class="fw-bold text-dark mb-1">Selamat Datang</h2>
            <p class="text-muted small">Portal layanan terpadu <?= DESA_NAMA ?></p>
        </div>

        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-<?= $flash['type']==='success'?'check-circle':'exclamation-triangle' ?>-fill fs-5 text-<?= $flash['type'] ?>"></i>
            <span class="small fw-semibold text-dark"><?= htmlspecialchars($flash['message']) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= APP_URL ?>/auth/auth/loginPost" id="loginForm">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-3">
                <label class="form-label small">Username atau Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="identifier" id="identifier" class="form-control border-start-0"
                           placeholder="Masukkan username atau email" required autocomplete="username">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0"
                           placeholder="Masukkan password" required autocomplete="current-password">
                    <button type="button" id="togglePass">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">Ingat saya</label>
                </div>
                <a href="#" class="small text-decoration-none fw-semibold" style="color: var(--accent-blue)">Lupa password?</a>
            </div>

            <button type="submit" class="btn-submit w-100 d-flex align-items-center justify-content-center gap-2" id="submitBtn">
                <i class="bi bi-box-arrow-in-right"></i>
                Masuk Ke Portal
            </button>
        </form>

        <!-- Demo Accounts Section -->
        <div class="mt-4">
            <div class="text-center position-relative mb-3">
                <hr class="my-0 text-muted opacity-25">
                <span class="bg-body px-3 position-absolute top-50 start-50 translate-middle small text-muted fw-semibold">
                    Akun Demo
                </span>
            </div>
            <div class="row g-2">
                <?php foreach([
                    ['warga','password123','Warga','primary'],
                    ['admin','password123','Admin','success'],
                    ['kepaladesa','password123','Kades','warning'],
                ] as [$user,$pass,$label,$color]): ?>
                <div class="col-4">
                    <button type="button" class="btn btn-outline-<?= $color ?> demo-btn w-100 py-2"
                             onclick="fillDemo('<?= $user ?>','<?= $pass ?>')">
                        <?= $label ?>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="text-center small text-muted mt-2 mb-0" style="font-size: 0.68rem">Klik salah satu akun demo di atas untuk mengisi otomatis</p>
        </div>

        <p class="text-center text-muted mt-5 mb-0" style="font-size: 0.72rem">
            <?= DESA_NAMA ?> &copy; <?= date('Y') ?> &middot; Portal v<?= APP_VERSION ?>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePass').addEventListener('click', function() {
    const p = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (p.type === 'password') {
        p.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        p.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    btn.disabled = true;
});

function fillDemo(username, password) {
    document.getElementById('identifier').value = username;
    document.getElementById('password').value = password;
    document.getElementById('identifier').focus();
}
</script>
</body>
</html>
