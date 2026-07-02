# SIAP-Desa — Panduan Instalasi

## Persyaratan Sistem

| Komponen | Versi Minimum |
|----------|---------------|
| PHP      | 8.3+          |
| MySQL    | 8.0+ / MariaDB 10.6+ |
| Apache   | 2.4+          |
| Python   | 3.11+         |
| XAMPP    | 8.2+          |

---

## 1. Setup Database

```sql
-- Di phpMyAdmin atau MySQL CLI:
SOURCE /path/to/desa-ai-system/database/migrations/001_create_tables.sql;
```

---

## 2. Konfigurasi PHP

Edit `config/config.php`:
```php
define('APP_URL', 'http://localhost/desa-ai-system');
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY');
define('AI_SERVER_KEY',  'your-secure-api-key');
```

Edit `config/database.php` (atau set environment variable):
```env
DB_HOST=127.0.0.1
DB_NAME=desa_ai_system
DB_USER=root
DB_PASS=your_password
```

---

## 3. Setup Apache (XAMPP)

Salin folder ke `C:\xampp\htdocs\desa-ai-system\` (Windows)
atau `/var/www/html/desa-ai-system/` (Linux).

Pastikan `mod_rewrite` aktif:
```apache
# httpd.conf — pastikan tidak ada comment (#)
LoadModule rewrite_module modules/mod_rewrite.so
AllowOverride All
```

Akses: `http://localhost/desa-ai-system`

---

## 4. Setup Python AI Server

```bash
cd ai_server/

# Buat virtual environment
python -m venv venv
source venv/bin/activate     # Linux/Mac
venv\Scripts\activate        # Windows

# Install dependencies
pip install -r requirements.txt

# Copy & konfigurasi .env
cp .env.example .env
# Edit .env: isi GEMINI_API_KEY dan AI_SERVER_KEY

# Jalankan server
python main.py
```

Server AI akan berjalan di: `http://127.0.0.1:8000`
Dokumentasi API: `http://127.0.0.1:8000/docs`

---

## 5. YOLOv8 Model (Opsional)

Untuk menggunakan model custom YOLOv8 yang sudah dilatih untuk infrastruktur desa:

```bash
# Install ultralytics
pip install ultralytics

# Download pretrained (auto-download saat pertama run)
# atau letakkan model custom di:
ai_server/models/yolov8n-desa.pt
```

---

## 6. Akun Default

| Role       | Username    | Password   |
|------------|-------------|------------|
| Warga      | warga       | password123 |
| Admin Desa | admin       | password123 |
| Kepala Desa| kepaladesa  | password123 |

> **Penting:** Ganti semua password default setelah instalasi!

---

## 7. Struktur URL

```
/                           → Redirect ke /login
/login                      → Halaman login
/auth/logout                → Logout
/warga/dashboard            → Dashboard Warga
/warga/surat/create         → Pengajuan Surat
/warga/surat/tracking       → Tracking Surat
/warga/pengaduan/create     → Buat Pengaduan
/warga/chatbot              → AI Chatbot
/admin/dashboard            → Dashboard Admin
/admin/penduduk             → Kelola Penduduk
/admin/surat                → Kelola Surat
/admin/pengaduan            → Kelola Pengaduan
/kepaladesa/dashboard       → Dashboard Kepala Desa
/kepaladesa/surat           → Persetujuan Surat
/kepaladesa/ai-analytics    → AI Analytics
```

---

## 8. Keamanan Production

Sebelum deploy ke VPS:

1. Set `APP_ENV=production` di config
2. Aktifkan HTTPS dan uncomment redirect di `.htaccess`
3. Ganti semua API key default
4. Atur `BCRYPT_COST=12` (sudah default)
5. Pastikan folder `uploads/` tidak dapat diakses langsung
6. Setup firewall: izinkan port 80, 443, dan 8000 (AI server internal saja)

---

## Struktur Folder

```
desa-ai-system/
├── index.php              # Front Controller
├── .htaccess              # Apache Rules + Security
├── config/
│   ├── config.php         # Konfigurasi aplikasi
│   └── database.php       # Konfigurasi database
├── core/
│   ├── App.php            # Router
│   ├── Controller.php     # Base Controller
│   ├── Database.php       # PDO Singleton
│   └── Session.php        # Session Manager
├── middleware/
│   ├── AuthMiddleware.php  # Cek autentikasi
│   ├── RBACMiddleware.php  # Role-Based Access Control
│   └── CsrfMiddleware.php  # CSRF Protection
├── repositories/          # Repository Pattern (Data Layer)
├── services/              # Service Layer (Business Logic)
├── controllers/           # MVC Controllers
│   ├── auth/
│   ├── warga/
│   ├── admin/
│   └── kepaladesa/
├── views/                 # Bootstrap 5 Views
│   ├── layouts/
│   ├── auth/
│   ├── warga/
│   ├── admin/
│   ├── kepaladesa/
│   └── errors/
├── database/
│   └── migrations/        # SQL Schema
├── public/
│   └── uploads/           # File uploads
└── ai_server/             # Python FastAPI
    ├── main.py            # Entry point
    ├── config.py          # Settings
    ├── services/
    │   ├── yolo_service.py   # YOLOv8 Computer Vision
    │   └── gemini_service.py # Gemini AI Chatbot
    ├── schemas/           # Pydantic Schemas
    ├── middleware/        # API Auth
    └── requirements.txt
```
