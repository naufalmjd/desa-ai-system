-- ============================================================
-- DATABASE: desa_ai_system
-- Engine  : InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE DATABASE IF NOT EXISTS `desa_ai_system`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `desa_ai_system`;

-- ─── ROLES ───────────────────────────────────────────────────────────────────
CREATE TABLE `roles` (
    `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(30)  NOT NULL UNIQUE COMMENT 'warga|admin|kepala_desa',
    `label`       VARCHAR(50)  NOT NULL,
    `permissions` JSON         NOT NULL DEFAULT ('[]'),
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`name`, `label`, `permissions`) VALUES
('warga',       'Warga',       '["dashboard_warga","pengajuan_surat","tracking_surat","pengaduan","chatbot","informasi_desa","notifikasi","profil"]'),
('admin',       'Admin Desa',  '["dashboard_admin","kelola_penduduk","kelola_surat","kelola_pengaduan","kelola_informasi","notifikasi","laporan","profil"]'),
('kepala_desa', 'Kepala Desa', '["dashboard_kades","persetujuan_surat","monitoring_pengaduan","monitoring_penduduk","ai_analytics","laporan","profil"]');

-- ─── USERS ───────────────────────────────────────────────────────────────────
CREATE TABLE `users` (
    `id`               BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `role_id`          TINYINT UNSIGNED NOT NULL,
    `username`         VARCHAR(50)  NOT NULL UNIQUE,
    `email`            VARCHAR(150) NOT NULL UNIQUE,
    `password`         VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
    `remember_token`   VARCHAR(100) NULL,
    `csrf_token`       VARCHAR(64)  NULL,
    `login_attempts`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `locked_until`     TIMESTAMP    NULL,
    `last_login_at`    TIMESTAMP    NULL,
    `last_login_ip`    VARCHAR(45)  NULL,
    `email_verified`   TINYINT(1)   NOT NULL DEFAULT 0,
    `is_active`        TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       TIMESTAMP    NULL,
    PRIMARY KEY (`id`),
    KEY `idx_users_role` (`role_id`),
    KEY `idx_users_username` (`username`),
    KEY `idx_users_email` (`email`),
    KEY `idx_users_active` (`is_active`, `deleted_at`),
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default users (password = Hash('password123'))
INSERT INTO `users` (`role_id`, `username`, `email`, `password`, `is_active`, `email_verified`) VALUES
(1, 'warga',       'warga@desa.id',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1),
(2, 'admin',       'admin@desa.id',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1),
(3, 'kepaladesa',  'kades@desa.id',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- ─── PENDUDUK ────────────────────────────────────────────────────────────────
CREATE TABLE `penduduk` (
    `id`              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`         BIGINT UNSIGNED  NULL COMMENT 'Link ke akun user jika ada',
    `nik`             CHAR(16)     NOT NULL UNIQUE,
    `no_kk`           CHAR(16)     NOT NULL,
    `nama`            VARCHAR(150) NOT NULL,
    `tempat_lahir`    VARCHAR(80)  NOT NULL,
    `tanggal_lahir`   DATE         NOT NULL,
    `jenis_kelamin`   ENUM('L','P') NOT NULL,
    `agama`           ENUM('Islam','Kristen','Katolik','Hindu','Budha','Konghucu','Lainnya') NOT NULL,
    `status_kawin`    ENUM('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') NOT NULL DEFAULT 'Belum Kawin',
    `pekerjaan`       VARCHAR(100) NOT NULL DEFAULT '-',
    `pendidikan`      ENUM('Tidak/Belum Sekolah','SD','SMP','SMA','D3','S1','S2','S3') NOT NULL DEFAULT 'Tidak/Belum Sekolah',
    `alamat`          TEXT         NOT NULL,
    `rt`              VARCHAR(5)   NOT NULL,
    `rw`              VARCHAR(5)   NOT NULL,
    `dusun`           VARCHAR(80)  NOT NULL DEFAULT '-',
    `desa`            VARCHAR(100) NOT NULL DEFAULT 'Sukamaju',
    `kecamatan`       VARCHAR(100) NOT NULL,
    `kabupaten`       VARCHAR(100) NOT NULL,
    `provinsi`        VARCHAR(100) NOT NULL,
    `kode_pos`        CHAR(5)      NULL,
    `no_hp`           VARCHAR(20)  NULL,
    `email`           VARCHAR(150) NULL,
    `foto_ktp`        VARCHAR(255) NULL,
    `status_penduduk` ENUM('Tetap','Sementara','Pindah','Meninggal') NOT NULL DEFAULT 'Tetap',
    `kewarganegaraan` ENUM('WNI','WNA') NOT NULL DEFAULT 'WNI',
    `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      TIMESTAMP    NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_penduduk_nik` (`nik`),
    KEY `idx_penduduk_user` (`user_id`),
    KEY `idx_penduduk_no_kk` (`no_kk`),
    KEY `idx_penduduk_nama` (`nama`),
    KEY `idx_penduduk_status` (`status_penduduk`, `deleted_at`),
    CONSTRAINT `fk_penduduk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── JENIS_SURAT ─────────────────────────────────────────────────────────────
CREATE TABLE `jenis_surat` (
    `id`              TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode`            VARCHAR(10)  NOT NULL UNIQUE,
    `nama`            VARCHAR(100) NOT NULL,
    `deskripsi`       TEXT         NULL,
    `persyaratan`     JSON         NOT NULL DEFAULT ('[]'),
    `template_path`   VARCHAR(255) NULL,
    `estimasi_hari`   TINYINT UNSIGNED NOT NULL DEFAULT 3,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_jenis_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `jenis_surat` (`kode`, `nama`, `deskripsi`, `persyaratan`, `estimasi_hari`) VALUES
('DOM',  'Surat Domisili',           'Surat keterangan tempat tinggal', '["Fotokopi KTP","Fotokopi KK","Surat Pengantar RT/RW"]', 2),
('SKU',  'Surat Keterangan Usaha',   'Surat untuk kegiatan usaha',      '["Fotokopi KTP","Fotokopi KK","Surat Pengantar RT/RW","Foto Tempat Usaha"]', 3),
('SKTM', 'Surat Tidak Mampu',        'Surat keterangan tidak mampu',    '["Fotokopi KTP","Fotokopi KK","Surat Pengantar RT/RW"]', 3),
('NIK',  'Surat Pengantar Nikah',    'Pengantar pernikahan',            '["Fotokopi KTP","Fotokopi KK","Pas Foto 3x4 (2 lembar)","Akta Kelahiran"]', 5),
('KEL',  'Surat Kelahiran',          'Keterangan kelahiran bayi',       '["Surat Keterangan Bidan/Dokter","Fotokopi KTP Kedua Orang Tua","Fotokopi KK"]', 2),
('KEM',  'Surat Kematian',           'Keterangan kematian penduduk',    '["Surat Keterangan Dokter/RS","Fotokopi KTP Almarhum","Fotokopi KK"]', 2),
('PND',  'Surat Pindah Penduduk',    'Keterangan pindah domisili',      '["Fotokopi KTP","Fotokopi KK","Surat Pengantar RT/RW"]', 5),
('KHL',  'Surat Kehilangan',         'Keterangan kehilangan dokumen',   '["Laporan Kepolisian","Fotokopi KTP","Surat Pernyataan"]', 2),
('SKL',  'Surat Keterangan Lain',    'Surat keterangan umum',           '["Fotokopi KTP","Fotokopi KK"]', 3);

-- ─── PENGAJUAN_SURAT ─────────────────────────────────────────────────────────
CREATE TABLE `pengajuan_surat` (
    `id`              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `nomor`           VARCHAR(30)  NOT NULL UNIQUE COMMENT 'SRT-YYYY-XXXXXX',
    `user_id`         BIGINT UNSIGNED  NOT NULL,
    `penduduk_id`     BIGINT UNSIGNED  NOT NULL,
    `jenis_surat_id`  TINYINT UNSIGNED NOT NULL,
    `keperluan`       TEXT         NOT NULL,
    `catatan_pemohon` TEXT         NULL,
    `status`          ENUM('menunggu','diverifikasi','diproses','menunggu_persetujuan','disetujui','selesai','ditolak') NOT NULL DEFAULT 'menunggu',
    `catatan_admin`   TEXT         NULL,
    `catatan_kades`   TEXT         NULL,
    `admin_id`        BIGINT UNSIGNED  NULL COMMENT 'Admin yang memverifikasi',
    `kades_id`        BIGINT UNSIGNED  NULL COMMENT 'Kepala desa yang menyetujui',
    `verified_at`     TIMESTAMP    NULL,
    `approved_at`     TIMESTAMP    NULL,
    `rejected_at`     TIMESTAMP    NULL,
    `selesai_at`      TIMESTAMP    NULL,
    `file_surat`      VARCHAR(255) NULL COMMENT 'Path file PDF',
    `qr_code`         VARCHAR(255) NULL,
    `tanda_tangan`    VARCHAR(255) NULL,
    `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      TIMESTAMP    NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_pengajuan_nomor` (`nomor`),
    KEY `idx_pengajuan_user` (`user_id`),
    KEY `idx_pengajuan_penduduk` (`penduduk_id`),
    KEY `idx_pengajuan_jenis` (`jenis_surat_id`),
    KEY `idx_pengajuan_status` (`status`),
    KEY `idx_pengajuan_admin` (`admin_id`),
    KEY `idx_pengajuan_kades` (`kades_id`),
    CONSTRAINT `fk_pengajuan_user`    FOREIGN KEY (`user_id`)        REFERENCES `users` (`id`),
    CONSTRAINT `fk_pengajuan_pddk`    FOREIGN KEY (`penduduk_id`)    REFERENCES `penduduk` (`id`),
    CONSTRAINT `fk_pengajuan_jenis`   FOREIGN KEY (`jenis_surat_id`) REFERENCES `jenis_surat` (`id`),
    CONSTRAINT `fk_pengajuan_admin`   FOREIGN KEY (`admin_id`)       REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_pengajuan_kades`   FOREIGN KEY (`kades_id`)       REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── LAMPIRAN_SURAT ──────────────────────────────────────────────────────────
CREATE TABLE `lampiran_surat` (
    `id`              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `pengajuan_id`    BIGINT UNSIGNED  NOT NULL,
    `nama_file`       VARCHAR(255) NOT NULL,
    `original_name`   VARCHAR(255) NOT NULL,
    `mime_type`       VARCHAR(100) NOT NULL,
    `ukuran`          INT UNSIGNED NOT NULL COMMENT 'bytes',
    `path`            VARCHAR(500) NOT NULL,
    `jenis_lampiran`  VARCHAR(100) NOT NULL COMMENT 'ktp|kk|pendukung',
    `is_valid`        TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_lampiran_pengajuan` (`pengajuan_id`),
    CONSTRAINT `fk_lampiran_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_surat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── PENGADUAN ───────────────────────────────────────────────────────────────
CREATE TABLE `pengaduan` (
    `id`              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `nomor`           VARCHAR(30)  NOT NULL UNIQUE COMMENT 'ADU-YYYY-XXXXXX',
    `user_id`         BIGINT UNSIGNED  NOT NULL,
    `judul`           VARCHAR(200) NOT NULL,
    `kategori`        ENUM('jalan_rusak','sampah','banjir','lampu_mati','pohon_tumbang','infrastruktur','lainnya') NOT NULL,
    `deskripsi`       TEXT         NOT NULL,
    `lokasi_alamat`   VARCHAR(300) NOT NULL,
    `latitude`        DECIMAL(10,8) NULL,
    `longitude`       DECIMAL(11,8) NULL,
    `status`          ENUM('menunggu','ditindaklanjuti','diproses','selesai','ditutup') NOT NULL DEFAULT 'menunggu',
    `prioritas`       ENUM('rendah','sedang','tinggi','kritis') NOT NULL DEFAULT 'sedang',
    `tanggapan_admin` TEXT         NULL,
    `admin_id`        BIGINT UNSIGNED  NULL,
    `tanggal_tindak`  TIMESTAMP    NULL,
    `selesai_at`      TIMESTAMP    NULL,
    `rating`          TINYINT UNSIGNED NULL COMMENT '1-5',
    `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      TIMESTAMP    NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_pengaduan_nomor` (`nomor`),
    KEY `idx_pengaduan_user` (`user_id`),
    KEY `idx_pengaduan_status` (`status`),
    KEY `idx_pengaduan_kategori` (`kategori`),
    KEY `idx_pengaduan_prioritas` (`prioritas`),
    KEY `idx_pengaduan_admin` (`admin_id`),
    CONSTRAINT `fk_pengaduan_user`  FOREIGN KEY (`user_id`)  REFERENCES `users` (`id`),
    CONSTRAINT `fk_pengaduan_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── PENGADUAN_MEDIA ─────────────────────────────────────────────────────────
CREATE TABLE `pengaduan_media` (
    `id`           BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `pengaduan_id` BIGINT UNSIGNED  NOT NULL,
    `tipe`         ENUM('foto','video') NOT NULL DEFAULT 'foto',
    `nama_file`    VARCHAR(255) NOT NULL,
    `original_name`VARCHAR(255) NOT NULL,
    `mime_type`    VARCHAR(100) NOT NULL,
    `ukuran`       INT UNSIGNED NOT NULL,
    `path`         VARCHAR(500) NOT NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_media_pengaduan` (`pengaduan_id`),
    CONSTRAINT `fk_media_pengaduan` FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── HASIL_AI ────────────────────────────────────────────────────────────────
CREATE TABLE `hasil_ai` (
    `id`              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `pengaduan_id`    BIGINT UNSIGNED  NOT NULL UNIQUE,
    `media_id`        BIGINT UNSIGNED  NOT NULL,
    `model`           VARCHAR(50)  NOT NULL DEFAULT 'YOLOv8',
    `kategori_deteksi`VARCHAR(100) NOT NULL,
    `confidence_score`DECIMAL(5,2) NOT NULL COMMENT '0-100',
    `prioritas_ai`    ENUM('rendah','sedang','tinggi','kritis') NOT NULL,
    `bounding_boxes`  JSON         NULL COMMENT 'Array of detected objects',
    `labels`          JSON         NULL,
    `raw_response`    JSON         NULL,
    `processing_time` DECIMAL(8,3) NULL COMMENT 'ms',
    `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ai_pengaduan` (`pengaduan_id`),
    KEY `idx_ai_media` (`media_id`),
    CONSTRAINT `fk_ai_pengaduan` FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduan` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ai_media`     FOREIGN KEY (`media_id`)     REFERENCES `pengaduan_media` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── CHAT_HISTORY ────────────────────────────────────────────────────────────
CREATE TABLE `chat_history` (
    `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED  NOT NULL,
    `session_id` VARCHAR(64)  NOT NULL,
    `role`       ENUM('user','assistant') NOT NULL,
    `content`    TEXT         NOT NULL,
    `tokens`     INT UNSIGNED NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_chat_user` (`user_id`),
    KEY `idx_chat_session` (`session_id`),
    CONSTRAINT `fk_chat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── CHAT_RATING ─────────────────────────────────────────────────────────────
CREATE TABLE `chat_rating` (
    `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `chat_id`    BIGINT UNSIGNED  NOT NULL,
    `user_id`    BIGINT UNSIGNED  NOT NULL,
    `rating`     TINYINT UNSIGNED NOT NULL COMMENT '1-5',
    `komentar`   TEXT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_rating_chat` (`chat_id`),
    KEY `idx_rating_user` (`user_id`),
    CONSTRAINT `fk_rating_chat` FOREIGN KEY (`chat_id`) REFERENCES `chat_history` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rating_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── BERITA ──────────────────────────────────────────────────────────────────
CREATE TABLE `berita` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED  NOT NULL,
    `judul`       VARCHAR(300) NOT NULL,
    `slug`        VARCHAR(320) NOT NULL UNIQUE,
    `kategori`    ENUM('berita','pengumuman','agenda','galeri') NOT NULL DEFAULT 'berita',
    `konten`      LONGTEXT     NOT NULL,
    `excerpt`     TEXT         NULL,
    `thumbnail`   VARCHAR(255) NULL,
    `tanggal`     DATE         NULL COMMENT 'Untuk agenda/jadwal',
    `waktu_mulai` TIME         NULL,
    `waktu_selesai` TIME       NULL,
    `lokasi`      VARCHAR(200) NULL,
    `status`      ENUM('draft','publish','arsip') NOT NULL DEFAULT 'draft',
    `views`       INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`  TIMESTAMP    NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_berita_slug` (`slug`),
    KEY `idx_berita_user` (`user_id`),
    KEY `idx_berita_kategori` (`kategori`),
    KEY `idx_berita_status` (`status`),
    CONSTRAINT `fk_berita_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── GALERI ──────────────────────────────────────────────────────────────────
CREATE TABLE `galeri` (
    `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED  NOT NULL,
    `judul`      VARCHAR(200) NOT NULL,
    `deskripsi`  TEXT         NULL,
    `tipe`       ENUM('foto','video') NOT NULL DEFAULT 'foto',
    `file_path`  VARCHAR(500) NOT NULL,
    `thumbnail`  VARCHAR(500) NULL,
    `url_video`  VARCHAR(500) NULL,
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP    NULL,
    PRIMARY KEY (`id`),
    KEY `idx_galeri_user` (`user_id`),
    KEY `idx_galeri_tipe` (`tipe`),
    CONSTRAINT `fk_galeri_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── BANTUAN_SOSIAL ──────────────────────────────────────────────────────────
CREATE TABLE `bantuan_sosial` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `nama`        VARCHAR(200) NOT NULL,
    `deskripsi`   TEXT         NOT NULL,
    `besaran`     VARCHAR(100) NOT NULL,
    `sasaran`     VARCHAR(200) NOT NULL,
    `syarat`      JSON         NOT NULL DEFAULT ('[]'),
    `periode`     VARCHAR(100) NULL,
    `status`      ENUM('aktif','pendaftaran','selesai','ditutup') NOT NULL DEFAULT 'aktif',
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── NOTIFIKASI ──────────────────────────────────────────────────────────────
CREATE TABLE `notifikasi` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED  NOT NULL,
    `judul`       VARCHAR(200) NOT NULL,
    `pesan`       TEXT         NOT NULL,
    `tipe`        ENUM('surat','pengaduan','sistem','info','bantuan') NOT NULL DEFAULT 'info',
    `url`         VARCHAR(500) NULL,
    `is_read`     TINYINT(1)   NOT NULL DEFAULT 0,
    `read_at`     TIMESTAMP    NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_notif_user` (`user_id`),
    KEY `idx_notif_read` (`is_read`),
    KEY `idx_notif_tipe` (`tipe`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── LOG_AKTIVITAS ───────────────────────────────────────────────────────────
CREATE TABLE `log_aktivitas` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED  NULL,
    `action`      VARCHAR(100) NOT NULL,
    `module`      VARCHAR(50)  NOT NULL,
    `description` TEXT         NULL,
    `data_before` JSON         NULL,
    `data_after`  JSON         NULL,
    `ip_address`  VARCHAR(45)  NULL,
    `user_agent`  VARCHAR(500) NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_log_user` (`user_id`),
    KEY `idx_log_action` (`action`),
    KEY `idx_log_module` (`module`),
    KEY `idx_log_created` (`created_at`),
    CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── LAPORAN ─────────────────────────────────────────────────────────────────
CREATE TABLE `laporan` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED  NOT NULL,
    `judul`       VARCHAR(200) NOT NULL,
    `tipe`        ENUM('surat','pengaduan','penduduk','keuangan','kinerja') NOT NULL,
    `periode_dari` DATE        NOT NULL,
    `periode_sampai` DATE      NOT NULL,
    `data`        JSON         NULL,
    `file_path`   VARCHAR(500) NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_laporan_user` (`user_id`),
    KEY `idx_laporan_tipe` (`tipe`),
    CONSTRAINT `fk_laporan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
