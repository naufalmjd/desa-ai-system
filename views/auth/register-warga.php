<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Warga — DigitalDesa.id</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* ============================================================
           DigitalDesa.id — Register Warga Page
           ============================================================ */
        :root {
            --primary:          #818cf8;
            --primary-dark:     #6366f1;
            --primary-glow:     rgba(129, 140, 248, 0.3);
            --accent:           #22d3ee;
            --accent-glow:      rgba(34, 211, 238, 0.25);
            --success:          #34d399;
            --danger:           #fb7185;
            --register-color:   #f59e0b;
            --register-dark:    #d97706;
            --register-glow:    rgba(245, 158, 11, 0.3);
        }

        [data-theme="dark"] {
            --bg-gradient:      radial-gradient(circle at 10% 20%, #0f172a 0%, #020617 100%);
            --bg-solid:         #020617;
            --card-bg:          rgba(30, 41, 59, 0.85);
            --card-border:      rgba(255, 255, 255, 0.08);
            --text-main:        #f1f5f9;
            --text-muted:       #cbd5e1;
            --shadow:           rgba(0, 0, 0, 0.7);
            --nav-bg:           rgba(8, 10, 16, 0.92);
            --input-bg:         rgba(30, 41, 59, 0.6);
            --register-bg:      rgba(245, 158, 11, 0.15);
            --register-border:  rgba(245, 158, 11, 0.3);
        }

        [data-theme="light"] {
            --bg-gradient:      radial-gradient(circle at 10% 20%, #f1f5f9 0%, #e2e8f0 100%);
            --bg-solid:         #e2e8f0;
            --card-bg:          rgba(255, 255, 255, 0.8);
            --card-border:      rgba(0, 0, 0, 0.08);
            --text-main:        #0f172a;
            --text-muted:       #475569;
            --shadow:           rgba(15, 23, 42, 0.08);
            --nav-bg:           rgba(226, 232, 240, 0.9);
            --input-bg:         rgba(255, 255, 255, 0.75);
            --register-bg:      rgba(245, 158, 11, 0.1);
            --register-border:  rgba(245, 158, 11, 0.2);
        }

        * {
            font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1rem;
            transition: background 0.4s ease, color 0.4s ease;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary-glow);
            border-radius: 4px;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: var(--card-bg) !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 24px;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            box-shadow: 0 20px 60px var(--shadow), 0 0 0 1px rgba(255,255,255,0.02);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 80px var(--shadow), 0 0 0 1px rgba(129, 140, 248, 0.1);
        }

        .register-container {
            max-width: 620px;
            width: 100%;
            margin: 0 auto;
        }

        /* Form Controls */
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--card-border) !important;
            color: var(--text-main) !important;
            border-radius: 12px;
            padding: 0.75rem 1.1rem;
            transition: all 0.25s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px var(--primary-glow) !important;
        }
        .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.5;
        }
        .form-label {
            font-weight: 600;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 0.3rem;
        }

        .input-group-text {
            background: var(--input-bg) !important;
            border: 1px solid var(--card-border) !important;
            color: var(--text-muted);
        }

        /* Buttons */
        .btn-register {
            background: linear-gradient(135deg, var(--register-color), var(--register-dark));
            color: #fff !important;
            border: none;
            border-radius: 12px;
            padding: 0.85rem 1.8rem;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 15px var(--register-glow);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(245, 158, 11, 0.45);
            filter: brightness(1.1);
        }
        .btn-register:disabled {
            opacity: 0.6;
            transform: none;
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--text-main) !important;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-outline-custom:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff !important;
            border: none;
            border-radius: 12px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            box-shadow: 0 4px 15px var(--primary-glow);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(99, 102, 241, 0.45);
            filter: brightness(1.1);
        }

        /* Text Gradient */
        .text-gradient {
            background: linear-gradient(135deg, #fff 30%, var(--text-muted) 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        [data-theme="light"] .text-gradient {
            background: linear-gradient(135deg, var(--text-main) 30%, var(--primary) 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--primary-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary);
            margin: 0 auto 1rem;
        }

        /* Password Strength */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 6px;
            transition: all 0.3s ease;
            background: var(--card-border);
        }
        .password-strength.weak { background: #fb7185; width: 25%; }
        .password-strength.medium { background: #fbbf24; width: 50%; }
        .password-strength.strong { background: #34d399; width: 75%; }
        .password-strength.very-strong { background: #22d3ee; width: 100%; }

        .requirement-item {
            font-size: 0.75rem;
            color: var(--text-muted);
            padding: 0.15rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .requirement-item.valid {
            color: var(--success);
        }
        .requirement-item.invalid {
            color: var(--danger);
        }
        .requirement-item .bi {
            font-size: 0.7rem;
        }

        /* Alert */
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        /* Theme Toggle */
        .theme-btn {
            cursor: pointer;
            font-size: 1.15rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            transition: color 0.2s ease;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 100;
        }
        .theme-btn:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }
        [data-theme="light"] .theme-btn:hover {
            background: rgba(0, 0, 0, 0.03);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }
        .delay-1 { animation-delay: 0.05s; opacity: 0; }
        .delay-2 { animation-delay: 0.1s; opacity: 0; }
        .delay-3 { animation-delay: 0.15s; opacity: 0; }
        .delay-4 { animation-delay: 0.2s; opacity: 0; }
        .delay-5 { animation-delay: 0.25s; opacity: 0; }
        .delay-6 { animation-delay: 0.3s; opacity: 0; }
        .delay-7 { animation-delay: 0.35s; opacity: 0; }
        .delay-8 { animation-delay: 0.4s; opacity: 0; }

        /* Responsive */
        @media (max-width: 576px) {
            body {
                padding: 1rem 0.75rem;
            }
            .register-container {
                max-width: 100%;
            }
            .glass-card {
                border-radius: 16px;
                padding: 1.25rem !important;
            }
        }
    </style>
</head>
<body>

    <!-- Theme Toggle -->
    <button id="theme-toggle-btn" class="theme-btn" title="Ganti Tema">
        <i id="theme-icon" class="bi bi-moon-stars-fill text-info"></i>
    </button>

    <div class="register-container animate-in">
        <div class="card glass-card p-4 p-md-5 border-0">
            
            <!-- Header -->
            <div class="text-center mb-4">
                <div class="logo-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h1 class="h3 fw-bold text-gradient mb-1">Daftar Akun Warga</h1>
                <p class="text-muted small" style="font-size: 0.85rem;">
                    Buat akun untuk mengakses layanan administrasi desa secara online
                </p>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($flash) && $flash): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible alert-custom d-flex align-items-center gap-2 border-0 shadow-sm mb-3" role="alert" style="background: <?= $flash['type'] === 'success' ? 'rgba(16,185,129,0.12)' : 'rgba(244,63,94,0.12)' ?> !important;">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill text-success' : 'exclamation-triangle-fill text-danger' ?> fs-5"></i>
                <span class="small fw-semibold <?= $flash['type'] === 'success' ? 'text-success' : 'text-danger' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(0.5);"></button>
            </div>
            <?php endif; ?>

            <!-- Form Registrasi -->
            <form id="registerForm" method="POST" action="<?= APP_URL ?>/auth/auth/registerWargaPost" class="row g-3">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                
                <!-- Username -->
                <div class="col-12 animate-in delay-1">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-1"></i>Username
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" id="username" class="form-control"
                               placeholder="Contoh: budi_santoso" required autofocus>
                    </div>
                    <small class="text-muted" style="font-size: 0.68rem;">Minimal 3 karakter, hanya huruf kecil, angka, dan underscore</small>
                </div>

                <!-- Email -->
                <div class="col-12 animate-in delay-2">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-1"></i>Alamat Email
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" id="email" class="form-control"
                               placeholder="Contoh: budi@email.com" required>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div class="col-12 animate-in delay-2">
                    <label for="nama_lengkap" class="form-label">
                        <i class="bi bi-card-text me-1"></i>Nama Lengkap
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control"
                               placeholder="Contoh: Budi Santoso" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="col-12 animate-in delay-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="password" class="form-control"
                               placeholder="Minimal 8 karakter" required minlength="8">
                        <button type="button" class="input-group-text border-start-0" id="togglePass" style="background: var(--input-bg); cursor: pointer;">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    
                    <!-- Password Strength -->
                    <div class="password-strength" id="passwordStrength"></div>
                    
                    <!-- Password Requirements -->
                    <div class="mt-2">
                        <div class="requirement-item" id="req-length">
                            <i class="bi bi-circle"></i>
                            <span>Minimal 8 karakter</span>
                        </div>
                        <div class="requirement-item" id="req-upper">
                            <i class="bi bi-circle"></i>
                            <span>Huruf kapital (A-Z)</span>
                        </div>
                        <div class="requirement-item" id="req-number">
                            <i class="bi bi-circle"></i>
                            <span>Angka (0-9)</span>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="col-12 animate-in delay-3">
                    <label for="password_confirm" class="form-label">
                        <i class="bi bi-shield-check me-1"></i>Konfirmasi Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control"
                               placeholder="Masukkan ulang password" required>
                    </div>
                    <small class="text-muted" id="passwordMatchMsg" style="font-size: 0.68rem;"></small>
                </div>

                <!-- NIK -->
                <div class="col-12 animate-in delay-4">
                    <label for="nik" class="form-label">
                        <i class="bi bi-card-heading me-1"></i>NIK (Nomor Induk Kependudukan)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                        <input type="text" name="nik" id="nik" class="form-control"
                               placeholder="16 digit angka" required pattern="[0-9]{16}" maxlength="16">
                    </div>
                    <small class="text-muted" style="font-size: 0.68rem;">16 digit angka</small>
                </div>

                <!-- No KK -->
                <div class="col-12 animate-in delay-4">
                    <label for="no_kk" class="form-label">
                        <i class="bi bi-house me-1"></i>Nomor Kartu Keluarga (KK)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-house-fill"></i></span>
                        <input type="text" name="no_kk" id="no_kk" class="form-control"
                               placeholder="16 digit angka" required pattern="[0-9]{16}" maxlength="16">
                    </div>
                    <small class="text-muted" style="font-size: 0.68rem;">16 digit angka</small>
                </div>

                <!-- Alamat -->
                <div class="col-12 animate-in delay-5">
                    <label for="alamat" class="form-label">
                        <i class="bi bi-geo-alt me-1"></i>Alamat Lengkap
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                        <textarea name="alamat" id="alamat" rows="2" class="form-control"
                                  placeholder="Contoh: Dusun Krajan RT 01 RW 02, Desa Sukamaju" required></textarea>
                    </div>
                </div>

                <!-- RT & RW -->
                <div class="col-6 animate-in delay-5">
                    <label for="rt" class="form-label">RT</label>
                    <input type="text" name="rt" id="rt" class="form-control" placeholder="RT" required>
                </div>
                <div class="col-6 animate-in delay-5">
                    <label for="rw" class="form-label">RW</label>
                    <input type="text" name="rw" id="rw" class="form-control" placeholder="RW" required>
                </div>

                <!-- Tanggal Lahir -->
                <div class="col-12 animate-in delay-6">
                    <label for="tanggal_lahir" class="form-label">
                        <i class="bi bi-calendar3 me-1"></i>Tanggal Lahir
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" required>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="col-6 animate-in delay-6">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <!-- Agama -->
                <div class="col-6 animate-in delay-6">
                    <label for="agama" class="form-label">Agama</label>
                    <select name="agama" id="agama" class="form-select" required>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Budha">Budha</option>
                        <option value="Konghucu">Konghucu</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- Pekerjaan -->
                <div class="col-12 animate-in delay-7">
                    <label for="pekerjaan" class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" id="pekerjaan" class="form-control"
                           placeholder="Contoh: Petani, Wiraswasta, Karyawan" required>
                </div>

                <!-- Submit Button -->
                <div class="col-12 d-grid mt-4 animate-in delay-8">
                    <button type="submit" class="btn btn-register py-3" id="submitBtn">
                        <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                    </button>
                </div>

                <!-- Login Link -->
                <div class="col-12 text-center mt-3 animate-in delay-8">
                    <p class="text-muted small mb-0">
                        Sudah punya akun? 
                        <a href="<?= APP_URL ?>/auth/login" class="text-primary text-decoration-none fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                        </a>
                    </p>
                </div>

                <!-- Kembali ke Beranda -->
                <div class="col-12 text-center animate-in delay-8">
                    <a href="<?= APP_URL ?>/" class="text-muted text-decoration-none small">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                    </a>
                </div>
            </form>

        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ============================================================
        // Register Warga — Frontend Logic
        // ============================================================

        // --- Theme Switcher ---
        function initTheme() {
            const btn = document.getElementById('theme-toggle-btn');
            const icon = document.getElementById('theme-icon');
            const currentTheme = localStorage.getItem('dd_theme') || 'dark';

            document.documentElement.setAttribute('data-theme', currentTheme);
            updateThemeIcon(currentTheme);

            btn.addEventListener('click', () => {
                const activeTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = activeTheme === 'light' ? 'dark' : 'light';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('dd_theme', newTheme);
                updateThemeIcon(newTheme);
            });

            function updateThemeIcon(theme) {
                if (theme === 'light') {
                    icon.className = 'bi bi-sun-fill text-warning';
                } else {
                    icon.className = 'bi bi-moon-stars-fill text-info';
                }
            }
        }

        // --- Password Visibility Toggle ---
        document.getElementById('togglePass')?.addEventListener('click', function() {
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

        // --- Password Strength Checker ---
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('passwordStrength');
        
        passwordInput?.addEventListener('input', function() {
            const val = this.value;
            let strength = 0;
            
            // Check requirements
            const hasLength = val.length >= 8;
            const hasUpper = /[A-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            
            if (hasLength) strength++;
            if (hasUpper) strength++;
            if (hasNumber) strength++;
            
            // Update strength bar
            const classes = ['', 'weak', 'medium', 'strong', 'very-strong'];
            strengthBar.className = 'password-strength';
            if (val.length > 0) {
                strengthBar.classList.add(classes[strength]);
            }
            
            // Update requirements
            updateRequirement('req-length', hasLength);
            updateRequirement('req-upper', hasUpper);
            updateRequirement('req-number', hasNumber);
        });

        function updateRequirement(id, valid) {
            const el = document.getElementById(id);
            if (!el) return;
            const icon = el.querySelector('.bi');
            if (valid) {
                el.classList.remove('invalid');
                el.classList.add('valid');
                icon.className = 'bi bi-check-circle-fill';
            } else {
                el.classList.remove('valid');
                el.classList.add('invalid');
                icon.className = 'bi bi-circle';
            }
        }

        // --- Password Match Checker ---
        const confirmPassword = document.getElementById('password_confirm');
        const matchMsg = document.getElementById('passwordMatchMsg');
        
        confirmPassword?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            if (this.value.length === 0) {
                matchMsg.textContent = '';
                matchMsg.className = 'text-muted';
                return;
            }
            if (this.value === password) {
                matchMsg.textContent = '✓ Password cocok';
                matchMsg.className = 'text-success';
            } else {
                matchMsg.textContent = '✗ Password tidak cocok';
                matchMsg.className = 'text-danger';
            }
        });

        // --- NIK & KK Validation (only numbers) ---
        document.getElementById('nik')?.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 16);
        });
        document.getElementById('no_kk')?.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 16);
        });

        // --- Username Validation ---
        document.getElementById('username')?.addEventListener('input', function() {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
        });

        // --- Form Submission ---
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtn');
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            
            // Validate password match
            if (password !== confirm) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Password dan konfirmasi password tidak cocok.',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }
            
            // Validate NIK
            const nik = document.getElementById('nik').value;
            if (nik.length !== 16) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'NIK harus 16 digit angka.',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }
            
            // Validate KK
            const noKK = document.getElementById('no_kk').value;
            if (noKK.length !== 16) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Nomor KK harus 16 digit angka.',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }
            
            // Disable button
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
            
            // Submit form via AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Pendaftaran Berhasil!',
                        text: data.message || 'Akun warga berhasil dibuat. Silakan login.',
                        icon: 'success',
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: 'Login Sekarang'
                    }).then(() => {
                        window.location.href = '<?= APP_URL ?>/auth/login';
                    });
                } else {
                    Swal.fire({
                        title: 'Pendaftaran Gagal',
                        text: data.message || 'Terjadi kesalahan, silakan coba lagi.',
                        icon: 'error',
                        confirmButtonColor: '#f43f5e'
                    });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Daftar Sekarang';
                }
            })
            .catch(() => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#f43f5e'
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Daftar Sekarang';
            });
        });

        // --- Set max date for tanggal lahir ---
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const maxDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());
            document.getElementById('tanggal_lahir').max = maxDate.toISOString().split('T')[0];
        });

        // --- Initialize Theme ---
        initTheme();
    </script>
</body>
</html>