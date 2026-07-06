<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalDesa.id — Portal Informasi & Layanan Darurat Desa</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* ============================================================
           DigitalDesa.id — Premium Glassmorphism Design System
           ============================================================ */
        :root {
            --primary:          #818cf8; /* Indigo */
            --primary-dark:     #6366f1;
            --primary-glow:     rgba(129, 140, 248, 0.3);
            --accent:           #22d3ee; /* Cyan */
            --accent-glow:      rgba(34, 211, 238, 0.25);
            --success:          #34d399; /* Emerald */
            --danger:           #fb7185; /* Rose */
            --register-color:   #f59e0b; /* Amber */
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
            overflow-x: hidden;
            transition: background 0.4s ease, color 0.4s ease;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary-glow);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: var(--card-bg) !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 20px;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            box-shadow: 0 8px 32px 0 var(--shadow), 0 0 0 1px rgba(255,255,255,0.02);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                        box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                        border-color 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-4px);
            border-color: rgba(129, 140, 248, 0.3) !important;
            box-shadow: 0 12px 40px var(--primary-glow), 0 0 0 1px rgba(129, 140, 248, 0.1);
        }

        /* Navbar Styling */
        .navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--card-border);
            padding: 0.88rem 1.5rem;
            z-index: 1050;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #fff 40%, var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        [data-theme="light"] .navbar-brand {
            background: linear-gradient(135deg, var(--text-main) 40%, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-link {
            font-weight: 500;
            font-size: 0.92rem;
            color: var(--text-muted) !important;
            padding: 0.5rem 1rem !important;
            border-radius: 10px;
            transition: all 0.2s ease;
            margin: 0 0.15rem;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--text-main) !important;
            background: rgba(255, 255, 255, 0.05);
        }
        [data-theme="light"] .nav-link:hover, [data-theme="light"] .nav-link.active {
            background: rgba(0, 0, 0, 0.04);
        }

        /* Hero Layout */
        .hero-sec {
            position: relative;
            padding: 8rem 0 5rem;
            overflow: hidden;
        }
        .hero-glow {
            position: absolute;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%);
            filter: blur(40px);
            z-index: -1;
        }
        .hero-glow-1 { top: 10%; left: 10%; }
        .hero-glow-2 { bottom: 10%; right: 10%; background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%); }

        .text-gradient {
            background: linear-gradient(135deg, #fff 20%, var(--text-muted) 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        [data-theme="light"] .text-gradient {
            background: linear-gradient(135deg, var(--text-main) 20%, var(--primary) 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Page Switcher Sections */
        .page-section {
            display: none;
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
        .page-section.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* Premium Buttons - Warna Indigo */
        .btn-premium {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff !important;
            border: none;
            border-radius: 12px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            box-shadow: 0 4px 15px var(--primary-glow);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.92rem;
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

        /* Register Button - Warna Amber/Kuning */
        .btn-register {
            background: linear-gradient(135deg, var(--register-color), var(--register-dark));
            color: #fff !important;
            border: none;
            border-radius: 12px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            box-shadow: 0 4px 15px var(--register-glow);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.92rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(245, 158, 11, 0.45);
            filter: brightness(1.1);
        }

        .btn-outline-premium {
            background: transparent;
            color: var(--text-main) !important;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            transition: all 0.25s ease;
            font-size: 0.92rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-outline-premium:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
        }
        [data-theme="light"] .btn-outline-premium:hover {
            background: rgba(0, 0, 0, 0.03);
        }

        .btn-emergency {
            background: linear-gradient(135deg, var(--danger), #e11d48);
            color: #fff !important;
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(244, 63, 94, 0.35);
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.7rem 1.8rem;
            font-size: 0.92rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-emergency:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 25px rgba(244, 63, 94, 0.55);
        }

        /* Inputs & Selections */
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
            box-shadow: 0 0 0 3px var(--primary-glow) !important;
        }
        .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.5;
        }

        .input-group-text {
            background: var(--input-bg) !important;
            border: 1px solid var(--card-border) !important;
            color: var(--text-muted);
        }

        /* Stats widgets */
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            background: var(--primary-glow);
            color: var(--primary);
            font-size: 1.5rem;
        }
        .feat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: radial-gradient(circle, var(--accent-glow) 0%, transparent 100%);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        /* Announcement banner style */
        .banner-announce {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.12), rgba(99, 102, 241, 0.06));
            border-left: 4px solid var(--accent);
        }

        /* Document download design */
        .doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.88rem 1.25rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            margin-bottom: 0.6rem;
            transition: all 0.2s ease;
        }
        .doc-item:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: var(--primary-glow);
        }
        .doc-badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.68rem;
            font-weight: 700;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .doc-badge.pdf  { background: rgba(244, 63, 94, 0.15); color: #fb7185; }
        .doc-badge.xls  { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .doc-badge.doc  { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }

        /* Pulsing indicator */
        .animate-pulse {
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        /* Theme Toggle Button */
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
        }
        .theme-btn:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }
        [data-theme="light"] .theme-btn:hover {
            background: rgba(0, 0, 0, 0.03);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px var(--primary-glow);
        }
        .btn-submit:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
            filter: brightness(1.1);
        }

        /* Login Left Panel Details */
        .login-info-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .login-info-box:hover {
            transform: translateY(-3px);
            border-color: var(--primary-glow);
        }


        /* Pastikan semua teks utama menggunakan variabel */
        .text-main {
            color: var(--text-main) !important;
        }
        .text-muted {
            color: var(--text-muted) !important;
        }
        .card-title, .card-text, .fs-6, .fw-bold {
            color: var(--text-main);
        }
        /* Banner pengumuman lebih solid */
        .banner-announce {
            background: rgba(34, 211, 238, 0.12) !important;
            border-left: 4px solid var(--accent);
            backdrop-filter: blur(8px);
        }
        .banner-announce p {
            color: var(--text-main) !important;
        }
        /* Statistik card */
        .stat-icon {
            background: var(--primary-glow);
            color: var(--primary);
        }
        /* Input dan select */
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            color: var(--text-main) !important;
        }
        .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.6;
        }
        .demo-btn {
            color: var(--text-main) !important;
        }
    </style>
</head>
<body>

    <!-- NAVBAR UTAMA -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#landing">
                <i class="bi bi-house-gear-fill text-accent"></i>
                <span>DigitalDesa.id</span>
            </a>
            <button class="navbar-toggler border-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#landing"><i class="bi bi-compass me-1"></i>Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#beranda"><i class="bi bi-grid-fill me-1"></i>Dashboard Desa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#berita"><i class="bi bi-newspaper me-1"></i>Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ambulans"><i class="bi bi-heart-pulse-fill me-1 text-danger animate-pulse"></i>Layanan Ambulans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak"><i class="bi bi-chat-left-dots-fill me-1"></i>Kontak</a>
                    </li>
                    <!-- Theme Toggle -->
                    <li class="nav-item px-2">
                        <button id="theme-toggle-btn" class="theme-btn" title="Ganti Tema">
                            <i id="theme-icon" class="bi bi-moon-stars-fill text-info"></i>
                        </button>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="#login" class="btn btn-premium btn-sm w-100 px-4">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login Portal
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN PORTAL WRAPPER -->
    <main class="container my-5 pt-4">

        <!-- 1. LANDING PAGE SECTION -->
        <section id="landing" class="page-section active">
            <!-- Hero Section -->
            <div class="hero-sec text-center">
                <div class="hero-glow hero-glow-1"></div>
                <div class="hero-glow hero-glow-2"></div>
                <div class="container py-4">
                    <h1 class="display-3 fw-bold text-gradient mb-3">DigitalDesa.id</h1>
                    <p class="lead text-muted mx-auto mb-4" style="max-width: 650px;">
                        Mewujudkan tata kelola desa yang transparan, pelayanan mandiri warga yang super cepat, dan kesiapsiagaan darurat 24 jam berbasis teknologi digital modern.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#login" class="btn btn-premium py-2.5 px-4 fs-6">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                        </a>
                        <a href="#beranda" class="btn btn-outline-premium py-2.5 px-4 fs-6">
                            Jelajahi Dashboard Publik
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tentang Website Section -->
            <div class="row align-items-center my-4 py-3">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="card glass-card p-4 border-0">
                        <h3 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Tentang Portal</h3>
                        <p class="text-muted mb-3" style="line-height: 1.7;">
                            Portal Sistem Informasi **DigitalDesa.id** dirancang untuk mempermudah koordinasi antara perangkat desa dan masyarakat. Dengan integrasi teknologi informasi, pengurusan administrasi surat-menyurat, pemantauan logistik desa, hingga penanganan darurat ambulans kini dapat diakses kapan saja dan di mana saja.
                        </p>
                        <p class="text-muted mb-0" style="line-height: 1.7;">
                            Kami berkomitmen memberikan keterbukaan informasi publik dan akuntabilitas anggaran desa guna mendukung terwujudnya konsep smart village di Indonesia.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=600&q=80" alt="Tentang Desa" class="img-fluid rounded-5 shadow-lg border border-secondary border-opacity-25" style="max-height: 320px; width: 100%; object-fit: cover;">
                </div>
            </div>

            <!-- Fitur Utama Section -->
            <div class="my-5">
                <h3 class="text-center fw-bold mb-4"><i class="bi bi-stars text-accent me-2"></i>Fitur Unggulan Sistem</h3>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card glass-card p-4 h-100 border-0">
                            <div class="feat-icon"><i class="bi bi-eye-fill"></i></div>
                            <h5 class="fw-bold mb-2">Transparansi Anggaran</h5>
                            <p class="text-muted mb-0" style="font-size: 0.88rem; line-height: 1.6;">Publikasi realisasi penggunaan anggaran Dana Desa (APBDes) yang akuntabel dan dapat diawasi langsung oleh seluruh warga.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card glass-card p-4 h-100 border-0">
                            <div class="feat-icon"><i class="bi bi-clock-fill"></i></div>
                            <h5 class="fw-bold mb-2">Layanan Cepat</h5>
                            <p class="text-muted mb-0" style="font-size: 0.88rem; line-height: 1.6;">Layanan permohonan surat administrasi desa yang instan, efisien, serta ramah perangkat seluler warganya.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card glass-card p-4 h-100 border-0">
                            <div class="feat-icon"><i class="bi bi-newspaper"></i></div>
                            <h5 class="fw-bold mb-2">Berita Terkini</h5>
                            <p class="text-muted mb-0" style="font-size: 0.88rem; line-height: 1.6;">Portal kabar, pengumuman darurat, agenda desa, dan dokumentasi kegiatan pembangunan yang terintegrasi rapi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- 2. HALAMAN BERANDA (Dashboard Publik) SECTION -->
        <section id="beranda" class="page-section">
            <div class="row g-4">
                
                <!-- Welcome & Announcement Banner -->
                <div class="col-lg-8">
                    <!-- Banner Pengumuman Penting Kades -->
                    <div class="card glass-card p-4 border-0 mb-4 banner-announce">
                        <div class="d-flex align-items-start gap-3">
                            <span class="fs-2 text-accent"><i class="bi bi-megaphone-fill"></i></span>
                            <div>
                                <h5 class="fw-bold mb-1">Pengumuman Penting Kepala Desa</h5>
                                <p class="text-muted mb-2" style="font-size: 0.88rem;">Diterbitkan pada: 04 Juli 2026</p>
                                <p class="text-main mb-0" style="font-size: 0.92rem; line-height: 1.6;">
                                    "Dihimbau kepada seluruh ketua RT/RW dan warga desa untuk ikut berpartisipasi dalam kegiatan kerja bakti massal pembersihan saluran irigasi pada hari Minggu, 12 Juli 2026 jam 07:00 WIB guna menyambut musim tanam."
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Desa Grid -->
                    <div class="my-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-2 text-accent"></i>Statistik Demografi Desa</h4>
                        <div class="row g-3">
                            <div class="col-6 col-sm-4">
                                <div class="card glass-card border-0 p-3 text-center">
                                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                                    <h3 class="fw-bold mb-0">3,450</h3>
                                    <small class="text-muted" style="font-size: 0.75rem;">Total Penduduk</small>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="card glass-card border-0 p-3 text-center">
                                    <div class="stat-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                    <h3 class="fw-bold mb-0">12.8 Km²</h3>
                                    <small class="text-muted" style="font-size: 0.75rem;">Luas Wilayah</small>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="card glass-card border-0 p-3 text-center">
                                    <div class="stat-icon"><i class="bi bi-houses-fill"></i></div>
                                    <h3 class="fw-bold mb-0">12 RT / 4 RW</h3>
                                    <small class="text-muted" style="font-size: 0.75rem;">Wilayah Administrasi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Widget Area -->
                <div class="col-lg-4">
                    <div class="card glass-card p-4 border-0 text-center h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(244,63,94,0.08), rgba(99,102,241,0.05)) !important;">
                        <div>
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center text-danger mx-auto mb-3" style="width: 70px; height: 70px;">
                                <i class="bi bi-heart-pulse-fill fs-1 animate-pulse"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Ambulans Darurat</h4>
                            <p class="text-muted mb-4" style="font-size: 0.88rem; line-height: 1.6;">
                                Butuh bantuan medis segera? Pesan Sopir Ambulans Desa langsung ke kontak WhatsApp admin siaga. Layanan aktif 24 jam bebas biaya bagi seluruh warga.
                            </p>
                        </div>
                        <a href="#ambulans" class="btn btn-emergency w-100 py-3 fs-6">
                            <i class="bi bi-telephone-fill me-2"></i>HUBUNGI AMBULANS DESA
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sekilas Berita Terbaru -->
            <div class="my-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0"><i class="bi bi-newspaper text-primary me-2"></i>Kabar & Rilis Berita Terbaru</h4>
                    <a href="#berita" class="btn btn-outline-premium btn-sm px-3">Lihat Semua Berita</a>
                </div>
                <div class="row" id="latest-news-grid">
                    <!-- Loaded dynamically via JS -->
                </div>
            </div>
        </section>


        <!-- 3. HALAMAN BERITA & LAYANAN DOKUMEN SECTION -->
        <section id="berita" class="page-section">
            <div class="text-center mb-5">
                <h2 class="fw-bold"><i class="bi bi-journal-text text-accent me-2"></i>Berita Desa & Pusat Lampiran</h2>
                <p class="text-muted">Pantau terus kegiatan pembangunan desa dan unduh berkas administrasi secara praktis.</p>
            </div>
            
            <div class="row" id="all-news-list">
                <!-- Loaded dynamically via JS -->
            </div>
        </section>


        <!-- 4. LAYANAN AMBULANS DARURAT SECTION -->
        <section id="ambulans" class="page-section">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card glass-card p-4 border-0 shadow-lg">
                        <div class="text-center mb-4">
                            <div class="badge bg-danger bg-opacity-25 text-danger px-3 py-2 fs-6 rounded-pill mb-2">
                                <i class="bi bi-circle-fill me-1 text-danger animate-pulse" style="font-size: 0.6rem;"></i> Layanan Siaga 24 Jam
                            </div>
                            <h3 class="fw-bold">Formulir Panggilan Ambulans</h3>
                            <p class="text-muted" style="font-size: 0.88rem;">Harap isi data pelapor dan titik jemput dengan jelas. Menekan tombol kirim akan langsung mengarahkan Anda ke WhatsApp Humas Desa untuk penjemputan.</p>
                        </div>

                        <form id="form-ambulans" class="row g-3">
                            <div class="col-12">
                                <label for="amb-nama" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Nama Pelapor / Keluarga</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="amb-nama" class="form-control" placeholder="Contoh: Budi Santoso" required>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label for="amb-alamat" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Lokasi / Alamat Lengkap Penjemputan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <textarea id="amb-alamat" rows="2" class="form-control" placeholder="Dusun, RT/RW, nomor rumah, atau patokan arah lokasi..." required></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="amb-kondisi" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Kondisi Darurat Pasien</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-activity"></i></span>
                                    <select id="amb-kondisi" class="form-select" required>
                                        <option value="" disabled selected>-- Pilih Kondisi Pasien --</option>
                                        <option value="Persalinan/Melahirkan">Persalinan / Melahirkan</option>
                                        <option value="Kecelakaan Lalu Lintas">Kecelakaan Lalu Lintas</option>
                                        <option value="Serangan Jantung/Nyeri Dada Parah">Serangan Jantung / Nyeri Dada Parah</option>
                                        <option value="Demam Tinggi / Kejang Anak">Demam Tinggi / Kejang Anak</option>
                                        <option value="Kedaruratan Medis Lainnya">Lainnya (Tuliskan di pesan WA nanti)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 d-grid mt-4">
                                <button type="submit" class="btn btn-emergency py-3 fs-6">
                                    <i class="bi bi-whatsapp me-2"></i>Pesan Ambulans Sekarang (Kirim WA)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>


        <!-- 5. HALAMAN KONTAK SECTION -->
        <section id="kontak" class="page-section">
            <div class="row g-4">
                
                <!-- Contact Info Area -->
                <div class="col-lg-5">
                    <div class="card glass-card p-4 border-0 h-100">
                        <h4 class="fw-bold mb-4"><i class="bi bi-telephone-inbound text-accent me-2"></i>Informasi Kontak Kantor Desa</h4>
                        
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <span class="fs-4 text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">Alamat Kantor</h6>
                                <p class="text-muted" style="font-size: 0.88rem;">Jl. Raya Demokrasi No. 45, Kecamatan Sukamaju, Kabupaten Wonosobo, Jawa Tengah, 56361</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3 mb-3">
                            <span class="fs-4 text-accent"><i class="bi bi-envelope-fill"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">Email Resmi</h6>
                                <p class="text-muted" style="font-size: 0.88rem;">info@digitaldesa.id &middot; desa.sukamaju@gmail.com</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3 mb-4">
                            <span class="fs-4 text-success"><i class="bi bi-telephone-fill"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">Telepon / WhatsApp Siaga</h6>
                                <p class="text-muted" style="font-size: 0.88rem;">+62 812-3456-789 (Humas Desa)</p>
                            </div>
                        </div>

                        <!-- Google Maps Placeholder -->
                        <div class="rounded-4 overflow-hidden border border-secondary border-opacity-25" style="height: 180px;">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126438.33806407062!2d109.83151978250645!3d-7.3596720516644265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7aa1a91e57c6b9%3A0x4027a7b50a25610!2sWonosobo%20Regency%2C%20Central%20Java!5e0!3m2!1sen!2sid!4v1704200000000!5m2!1sen!2sid" 
                                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>

                <!-- Contact Message Form -->
                <div class="col-lg-7">
                    <div class="card glass-card p-4 border-0 h-100">
                        <h4 class="fw-bold mb-3"><i class="bi bi-send-fill text-primary me-2"></i>Kirim Pesan Aspirasi Warga</h4>
                        <p class="text-muted mb-4" style="font-size: 0.88rem;">Ada saran, aduan, kritik, atau pertanyaan? Kirim pesan Anda di bawah ini secara rahasia dan aman.</p>
                        
                        <form id="form-kontak" class="row g-3">
                            <div class="col-md-6">
                                <label for="c-nama" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Nama Anda</label>
                                <input type="text" id="c-nama" class="form-control" placeholder="Contoh: Rian Pratama" required>
                            </div>
                            <div class="col-md-6">
                                <label for="c-email" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Alamat Email</label>
                                <input type="email" id="c-email" class="form-control" placeholder="Contoh: rian@email.com" required>
                            </div>
                            <div class="col-12">
                                <label for="c-subjek" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Subjek Aspirasi</label>
                                <input type="text" id="c-subjek" class="form-control" placeholder="Contoh: Pengaduan Saluran Air Mampet" required>
                            </div>
                            <div class="col-12">
                                <label for="c-pesan" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Isi Pesan Detail</label>
                                <textarea id="c-pesan" rows="4" class="form-control" placeholder="Tuliskan keluhan atau saran Anda dengan jelas..." required></textarea>
                            </div>
                            <div class="col-12 d-grid mt-4">
                                <button type="submit" class="btn btn-premium py-2.5">
                                    <i class="bi bi-send-fill me-1"></i>Kirim Aspirasi Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>


        <!-- 6. HALAMAN LOGIN SECTION (REAL LOGIN PORTAL) -->
        <section id="login" class="page-section">
            <div class="row g-4 align-items-stretch justify-content-center">
                
                <!-- Left Description Column -->
                <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-4 glass-card border-0" style="background: linear-gradient(135deg, rgba(15,23,42,0.6) 0%, rgba(8,10,16,0.8) 100%) !important;">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-10">
                                <i class="bi bi-buildings-fill text-white fs-4"></i>
                            </div>
                            <div>
                                <div class="text-white fw-bold fs-5"><?= DESA_NAMA ?></div>
                                <small class="text-muted"><?= DESA_KAB ?>, <?= DESA_PROV ?></small>
                            </div>
                        </div>
                        <h2 class="text-white fw-black fs-2 lh-sm mb-3">
                            Sistem Pelayanan Administrasi Desa
                        </h2>
                        <p class="text-white mb-4" style="font-size:0.9rem; line-height: 1.6 ; ">
                            Selamat datang di portal login perangkat desa. Silakan masuk untuk mengelola persuratan warga, memoderasi pengaduan, log aktivitas kecerdasan buatan, serta melacak log audit sistem informasi.
                        </p>
                    </div>

                    <!-- Statistics grid inside login area -->
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="login-info-box">
                                <i class="bi bi-people-fill text-info fs-5 mb-1 d-block"></i>
                                <div class="text-white fw-bold" style="font-size: 1rem;">3.842</div>
                                <small class="text-muted" style="font-size: 0.68rem">Warga</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="login-info-box">
                                <i class="bi bi-file-earmark-text-fill text-warning fs-5 mb-1 d-block"></i>
                                <div class="text-white fw-bold" style="font-size: 1rem;">128</div>
                                <small class="text-muted" style="font-size: 0.68rem">Persuratan</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="login-info-box">
                                <i class="bi bi-shield-fill-check text-success fs-5 mb-1 d-block"></i>
                                <div class="text-white fw-bold" style="font-size: 1rem;">95%</div>
                                <small class="text-muted" style="font-size: 0.68rem">Aduan Beres</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Form Column (Real Submit Form) -->
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="card glass-card p-4 border-0 h-100 d-flex flex-column justify-content-center">
                        <div class="text-center mb-4">
                            <div class="rounded-4 d-inline-flex p-3 mb-2 text-primary" style="background: rgba(99,102,241,0.08) !important">
                                <i class="bi bi-shield-lock-fill fs-3 text-primary"></i>
                            </div>
                            <h3 class="fw-bold mb-1">Masuk Portal</h3>
                            <p class="text-muted small">Silakan masuk menggunakan akun yang terdaftar</p>
                        </div>

                        <!-- PHP Flash Messages -->
                        <?php if (isset($flash) && $flash): ?>
                        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3" role="alert" style="background: <?= $flash['type'] === 'success' ? 'rgba(16,185,129,0.12)' : 'rgba(244,63,94,0.12)' ?> !important;">
                            <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill text-success' : 'exclamation-triangle-fill text-danger' ?> fs-5"></i>
                            <span class="small fw-semibold <?= $flash['type'] === 'success' ? 'text-success' : 'text-danger' ?>">
                                <?= htmlspecialchars($flash['message']) ?>
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(0.5);"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Real Authentication Submit Form -->
                        <form method="POST" action="<?= APP_URL ?>/auth/auth/loginPost" id="loginForm" class="row g-3">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Username atau Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="identifier" id="identifier" class="form-control"
                                           placeholder="Masukkan username atau email" required autocomplete="username">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.82rem;">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="Masukkan password" required autocomplete="current-password">
                                    <button type="button" class="input-group-text border-start-0" id="togglePass" style="background: var(--input-bg); cursor: pointer;">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-between align-items-center mt-3" style="font-size: 0.82rem;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" style="cursor: pointer;">
                                    <label class="form-check-label text-muted" for="remember" style="cursor: pointer;">Ingat Saya</label>
                                </div>
                                <a href="javascript:void(0)" onclick="Swal.fire('Fitur Lupa Password', 'Silakan hubungi administrator IT desa untuk mereset kata sandi Anda.', 'info')" class="text-accent text-decoration-none">Lupa Password?</a>
                            </div>

                            <div class="col-12 d-grid mt-4">
                                <button type="submit" class="btn-submit w-100 d-flex align-items-center justify-content-center gap-2" id="submitBtn">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    Masuk Ke Portal
                                </button>
                            </div>
                        </form>

                        <!-- Tombol Daftar Akun Warga - Warna Kuning/Amber -->
                        <div class="col-12 d-grid mt-4">
                            <a href="<?= APP_URL ?>/auth/register-warga" class="btn btn-register w-100 d-flex align-items-center justify-content-center gap-2" id="registerBtn">
                                <i class="bi bi-person-plus"></i>
                                Daftar Akun Warga
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </main>

    <!-- FOOTER UTAMA -->
    <footer class="text-center py-4 border-top border-secondary border-opacity-10 mt-5">
        <div class="container">
            <p class="text-muted m-0" style="font-size: 0.82rem;">
                &copy; 2026 <strong>DigitalDesa.id</strong>. Hak Cipta Dilindungi Undang-Undang.
            </p>
            <small class="text-accent" style="font-size: 0.72rem; font-weight: 500;">
                Smart Village Initiative &middot; Yogyakarta Digital System
            </small>
        </div>
    </footer>

    <!-- MODAL DETAIL BERITA & DOKUMEN -->
    <div class="modal fade" id="newsDetailModal" tabindex="-1" aria-labelledby="newsDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content glass-card border-0 p-2" style="background: var(--bg-solid) !important;">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-white" id="newsDetailModalLabel">Judul Berita</h5>
                        <small class="text-accent" id="modal-news-date">Tanggal</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body">
                    <img src="" id="modal-news-img" class="img-fluid rounded-4 mb-3" alt="Berita" style="width: 100%; max-height: 380px; object-fit: cover;">
                    <div id="modal-news-body" class="text-muted mb-4">
                        <!-- Content -->
                    </div>
                    <div id="modal-news-attachments">
                        <!-- Attachment Documents list -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS (termasuk Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ============================================================
        // DigitalDesa.id — Frontend Logic & Router
        // ============================================================

        // --- Mock Data: Berita & Dokumen ---
        const MOCK_BERITA = [
            {
                id: 1,
                title: "Pembangunan Jalan Usaha Tani Dusun Harapan Selesai 100%",
                date: "2026-07-05",
                image: "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=600&q=80",
                summary: "Pemerintah Desa sukses merampungkan pengaspalan jalan tani sepanjang 1.2 Km guna meningkatkan mobilitas pertanian warga.",
                content: "Proyek pengaspalan jalan usaha tani di Dusun Harapan yang didanai oleh Anggaran Dana Desa (ADD) Tahap I Tahun 2026 akhirnya selesai sepenuhnya. Pembangunan ini disambut antusias oleh para petani setempat karena memangkas waktu tempuh pengangkutan hasil panen padi dan jagung secara signifikan. Kepala Desa menyatakan bahwa pembangunan infrastruktur pertanian akan terus diprioritaskan demi ketahanan pangan lokal.",
                attachments: [
                    { name: "Laporan Realisasi Pembangunan Jalan.pdf", type: "pdf", size: "1.2 MB" },
                    { name: "Lampiran Anggaran Biaya Proyek.xlsx", type: "xls", size: "320 KB" }
                ]
            },
            {
                id: 2,
                title: "Penyaluran Bantuan Langsung Tunai (BLT) Dana Desa Tahap II",
                date: "2026-07-02",
                image: "https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?auto=format&fit=crop&w=600&q=80",
                summary: "Sebanyak 120 Keluarga Penerima Manfaat (KPM) menerima dana BLT untuk membantu pemenuhan kebutuhan pangan harian.",
                content: "Bertempat di balai desa, penyaluran BLT Dana Desa untuk periode April-Juni 2026 telah terlaksana dengan tertib. Sebanyak 120 KPM yang lolos verifikasi menerima bantuan tunai sebesar Rp 900.000 (Sembilan Ratus Ribu Rupiah). Proses penyaluran diawasi langsung oleh Babinsa, Bhabinkamtibmas, serta perwakilan Badan Permusyawaratan Desa (BPD) guna menjamin akuntabilitas dan ketepatan sasaran.",
                attachments: [
                    { name: "Daftar Penerima KPM BLT Tahap II.pdf", type: "pdf", size: "850 KB" },
                    { name: "Formulir Pendaftaran Bantuan Susulan.docx", type: "doc", size: "150 KB" }
                ]
            },
            {
                id: 3,
                title: "Program Penyuluhan Kesehatan Ibu & Anak Serta Imunisasi Gratis",
                date: "2026-06-28",
                image: "https://images.unsplash.com/photo-1505751172876-fa1923c5c528?auto=format&fit=crop&w=600&q=80",
                summary: "Kader Posyandu menggelar kegiatan bulanan pemeriksaan tumbuh kembang balita dan pencegahan stunting.",
                content: "Dalam upaya menekan angka stunting, Puskesmas Desa berkolaborasi dengan PKK menggelar Penyuluhan Kesehatan Ibu dan Anak serta pemberian vitamin A gratis. Warga yang memiliki bayi di bawah dua tahun mendapatkan penyuluhan menu gizi seimbang lokal. Kader posyandu juga melaksanakan pengukuran berat dan tinggi badan balita serta menyalurkan bantuan susu formula khusus bagi balita terindikasi stunting.",
                attachments: [
                    { name: "Panduan Gizi Mencegah Stunting Anak.pdf", type: "pdf", size: "2.1 MB" }
                ]
            }
        ];

        // --- SPA Routing System ---
        function initRouter() {
            const hasFlash = <?= isset($flash) && $flash ? 'true' : 'false' ?>;
            
            // Redirect to #login if there is an authentication error
            if (hasFlash && !window.location.hash) {
                window.location.hash = '#login';
            }

            const routePage = () => {
                const hash = window.location.hash || '#landing';
                const pageId = hash.substring(1);
                
                // Hide all sections
                document.querySelectorAll('.page-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                // Show target section
                const activeSection = document.getElementById(pageId);
                if (activeSection) {
                    activeSection.classList.add('active');
                    window.scrollTo(0, 0);
                } else {
                    document.getElementById('landing').classList.add('active');
                }
                
                // Update active link state in navbar
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                    const href = link.getAttribute('href');
                    if (href === hash) {
                        link.classList.add('active');
                    }
                });
            };

            window.addEventListener('hashchange', routePage);
            window.addEventListener('load', routePage);
        }

        // --- Render Kabar Berita & Dokumen ---
        function renderPublicData() {
            // 1. Render News preview on Dashboard (3 latest news)
            const newsContainer = document.getElementById('latest-news-grid');
            if (newsContainer) {
                newsContainer.innerHTML = MOCK_BERITA.map(news => `
                    <div class="col-md-4 mb-4">
                        <div class="card glass-card h-100 overflow-hidden border-0">
                            <img src="${news.image}" class="card-img-top" alt="${news.title}" style="height: 180px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <span class="text-accent mb-2 d-block" style="font-size: 0.78rem;">
                                    <i class="bi bi-calendar3 me-1"></i> ${formatDate(news.date)}
                                </span>
                                <h5 class="card-title fw-bold text-white fs-6 mb-2 text-truncate-2">${news.title}</h5>
                                <p class="card-text text-muted mb-4" style="font-size: 0.85rem; line-height: 1.5;">${news.summary}</p>
                                <button onclick="viewNewsDetail(${news.id})" class="btn btn-outline-premium btn-sm mt-auto w-100">
                                    Baca Selengkapnya
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // 2. Render News list on Halaman Berita
            const allNewsContainer = document.getElementById('all-news-list');
            if (allNewsContainer) {
                allNewsContainer.innerHTML = MOCK_BERITA.map(news => `
                    <div class="col-12 mb-4">
                        <div class="card glass-card border-0 overflow-hidden p-3 p-md-4">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-4">
                                    <img src="${news.image}" class="img-fluid rounded-4" alt="${news.title}" style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <span class="text-accent mb-2 d-block" style="font-size: 0.78rem;">
                                        <i class="bi bi-calendar3 me-1"></i> ${formatDate(news.date)}
                                    </span>
                                    <h4 class="text-white fw-bold mb-2">${news.title}</h4>
                                    <p class="text-muted mb-3" style="font-size: 0.88rem; line-height: 1.6;">${news.summary}</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button onclick="viewNewsDetail(${news.id})" class="btn btn-premium btn-sm px-4">
                                            Detail Berita & Berkas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        // --- Format Tanggal ---
        function formatDate(dateStr) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString('id-ID', options);
        }

        // --- View News Detail & Document Download ---
        function viewNewsDetail(newsId) {
            const news = MOCK_BERITA.find(b => b.id === newsId);
            if (!news) return;

            document.getElementById('newsDetailModalLabel').innerText = news.title;
            document.getElementById('modal-news-date').innerHTML = `<i class="bi bi-calendar3 me-1"></i> ${formatDate(news.date)}`;
            document.getElementById('modal-news-img').src = news.image;
            document.getElementById('modal-news-body').innerHTML = `<p style="line-height: 1.7; font-size: 0.92rem;">${news.content}</p>`;

            const attachmentContainer = document.getElementById('modal-news-attachments');
            if (news.attachments && news.attachments.length > 0) {
                attachmentContainer.innerHTML = `
                    <div class="border-top border-secondary pt-3 mt-4">
                        <h6 class="fw-bold text-white mb-3"><i class="bi bi-paperclip me-1 text-accent"></i>Lampiran Dokumen Pelayanan</h6>
                        ${news.attachments.map(doc => `
                            <div class="doc-item">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="doc-badge ${doc.type}">${doc.type}</span>
                                    <span class="text-white fw-semibold" style="font-size: 0.85rem;">${doc.name}</span>
                                    <small class="text-muted">(${doc.size})</small>
                                </div>
                                <button onclick="downloadMockFile('${doc.name}')" class="btn btn-sm btn-outline-info p-1 border-0" title="Unduh File">
                                    <i class="bi bi-download fs-5"></i>
                                </button>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                attachmentContainer.innerHTML = '';
            }

            const myModal = new bootstrap.Modal(document.getElementById('newsDetailModal'));
            myModal.show();
        }

        // --- Download File Simulation ---
        function downloadMockFile(fileName) {
            Swal.fire({
                title: 'Mengunduh Berkas...',
                text: `Memulai unduhan untuk "${fileName}"`,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // --- WhatsApp Ambulance Booking ---
        function handleAmbulanceOrder(event) {
            event.preventDefault();
            
            const nama = document.getElementById('amb-nama').value.trim();
            const alamat = document.getElementById('amb-alamat').value.trim();
            const kondisi = document.getElementById('amb-kondisi').value;
            
            if (!nama || !alamat || !kondisi) {
                Swal.fire('Error!', 'Mohon isi semua data formulir darurat.', 'error');
                return;
            }
            
            const adminPhone = "628123456789";
            
            const textMsg = `Halo Admin DigitalDesa, saya memerlukan bantuan AMBULANS SEGERA.
            
Nama Pelapor: ${nama}
Lokasi Penjemputan: ${alamat}
Kondisi Darurat: ${kondisi}

Mohon admin segera mencarikan sopir ambulans desa yang sedang ready/siap bertugas.`;
            
            const url = `https://api.whatsapp.com/send?phone=${adminPhone}&text=${encodeURIComponent(textMsg)}`;
            
            Swal.fire({
                title: 'Menghubungkan ke WhatsApp...',
                text: 'Formulir ambulans darurat akan langsung dikirim ke WhatsApp Admin Desa.',
                icon: 'info',
                confirmButtonText: 'Lanjutkan',
                confirmButtonColor: '#f43f5e'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(url, '_blank');
                    document.getElementById('form-ambulans').reset();
                }
            });
        }

        // --- Contact Form Submission ---
        function handleContactSubmit(event) {
            event.preventDefault();
            const nama = document.getElementById('c-nama').value;
            const email = document.getElementById('c-email').value;
            const subjek = document.getElementById('c-subjek').value;
            const pesan = document.getElementById('c-pesan').value;

            if (!nama || !email || !subjek || !pesan) {
                Swal.fire('Peringatan', 'Silakan isi semua data kontak.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Pesan Terkirim!',
                text: `Terima kasih ${nama}, pesan Anda mengenai "${subjek}" telah diterima oleh perangkat desa.`,
                icon: 'success',
                confirmButtonColor: '#6366f1'
            });

            document.getElementById('form-kontak').reset();
        }

        // --- autofill demo accounts ---
        function fillDemo(username, password) {
            document.getElementById('identifier').value = username;
            document.getElementById('password').value = password;
            document.getElementById('identifier').focus();
        }

        // --- password visibility toggle ---
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

        // --- form submission loader ---
        document.getElementById('loginForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
            btn.disabled = true;
        });

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

        // --- App Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            initRouter();
            renderPublicData();
            initTheme();
            
            document.getElementById('form-ambulans')?.addEventListener('submit', handleAmbulanceOrder);
            document.getElementById('form-kontak')?.addEventListener('submit', handleContactSubmit);
        });
    </script>
</body>
</html>