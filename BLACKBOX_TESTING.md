# DOKUMEN PENGUJIAN BLACK BOX (BLACK BOX TESTING)
## Sistem Informasi dan Administrasi Pelayanan Desa Berbasis AI (SIAP-Desa)

Dokumen ini memuat seluruh skenario pengujian fungsionalitas (*black box testing*) untuk aplikasi **SIAP-Desa**. Pengujian ini difokuskan pada pengujian antarmuka, input, output, otorisasi, dan integrasi fitur AI (YOLOv8 & Gemini) tanpa melihat struktur internal kode program.

---

### Daftar Isi
1. [Metodologi Pengujian](#metodologi-pengujian)
2. [Lingkungan Pengujian](#lingkungan-pengujian)
3. [Pengujian Modul 1: Autentikasi & Hak Akses (RBAC)](#pengujian-modul-1-autentikasi--hak-akses-rbac)
4. [Pengujian Modul 2: Kelola Data Penduduk (Admin)](#pengujian-modul-2-kelola-data-penduduk-admin)
5. [Pengujian Modul 3: Pengajuan & Pelacakan Surat (Warga)](#pengujian-modul-3-pengajuan--pelacakan-surat-warga)
6. [Pengujian Modul 4: Verifikasi & Persetujuan Surat (Admin & Kepala Desa)](#pengujian-modul-4-verifikasi--persetujuan-surat-admin--kepala-desa)
7. [Pengujian Modul 5: Pengaduan Masyarakat & Integrasi Deteksi AI YOLOv8 (Warga & Admin)](#pengujian-modul-5-pengaduan-masyarakat--integrasi-deteksi-ai-yolov8-warga--admin)
8. [Pengujian Modul 6: Chatbot AI Asisten Desa (Warga)](#pengujian-modul-6-chatbot-ai-asisten-desa-warga)
9. [Pengujian Modul 7: Laporan & AI Analytics (Kepala Desa & Admin)](#pengujian-modul-7-laporan--ai-analytics-kepala-desa--admin)
10. [Pengujian Modul 8: Kelola Bantuan Sosial (Admin & Warga)](#pengujian-modul-8-kelola-bantuan-sosial-admin--warga)
11. [Pengujian Modul 9: Manajemen User & Sistem Backup (Superadmin)](#pengujian-modul-9-manajemen-user--sistem-backup-superadmin)
12. [Kesimpulan Hasil Pengujian](#kesimpulan-hasil-pengujian)

---

### Metodologi Pengujian
Pengujian dilakukan menggunakan teknik **Equivalence Partitioning** dan **Boundary Value Analysis** pada form input, serta pengujian skenario fungsionalitas alur kerja (*use case workflow*). 

Setiap butir pengujian memiliki status kelulusan sebagai berikut:
- **Valid (Sesuai)**: Hasil aktual sesuai dengan hasil yang diharapkan.
- **Tidak Valid (Gagal)**: Hasil aktual berbeda dengan hasil yang diharapkan.

---

### Lingkungan Pengujian
- **Web Server**: Apache/2.4 (XAMPP)
- **Database Server**: MySQL 8.0 / MariaDB 10.6
- **Backend Language**: PHP 8.3 (MVC Pattern)
- **AI Server**: Python 3.11+ (FastAPI, PyTorch, YOLOv8, Gemini API)
- **Browser**: Google Chrome / Mozilla Firefox (terbaru)

---

### Pengujian Modul 1: Autentikasi & Hak Akses (RBAC)

Modul ini memvalidasi proses login, proteksi session, keamanan token CSRF, pembatasan percobaan login (rate limiting/lockout), dan pembatasan hak akses berdasarkan role (Warga, Admin, Kepala Desa, Superadmin).

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **AUTH-01** | Login dengan kredensial valid (Warga) | Input `username`: `warga`<br>Input `password`: `password123`<br>Klik tombol "Login" | Sistem berhasil memvalidasi kredensial, membuat session warga, dan mengarahkan (*redirect*) ke dashboard warga (`/warga/dashboard`). | Berhasil masuk ke dashboard warga. | **Valid** |
| **AUTH-02** | Login dengan kredensial valid (Admin) | Input `username`: `admin`<br>Input `password`: `password123`<br>Klik tombol "Login" | Sistem berhasil mengarahkan ke dashboard admin (`/admin/dashboard`). | Berhasil masuk ke dashboard admin. | **Valid** |
| **AUTH-03** | Login dengan kredensial valid (Kepala Desa) | Input `username`: `kepaladesa`<br>Input `password`: `password123`<br>Klik tombol "Login" | Sistem berhasil mengarahkan ke dashboard kepala desa (`/kepaladesa/dashboard`). | Berhasil masuk ke dashboard kades. | **Valid** |
| **AUTH-04** | Login dengan username/email tidak terdaftar | Input `username`: `siapasaja`<br>Input `password`: `password123`<br>Klik tombol "Login" | Sistem menolak akses, menampilkan pesan error flash: *"Username/email dan password salah atau akun tidak aktif."* | Muncul pesan error dan kembali ke halaman login. | **Valid** |
| **AUTH-05** | Login dengan password salah | Input `username`: `warga`<br>Input `password`: `salah123`<br>Klik tombol "Login" | Sistem menolak akses, mencatat percobaan login gagal, dan menampilkan pesan kesalahan. | Muncul pesan error dan kembali ke halaman login. | **Valid** |
| **AUTH-06** | Validasi input kosong (empty fields) | Kosongkan username/password<br>Klik tombol "Login" | Sistem memicu validasi client-side (HTML5 `required`) dan menampilkan pesan error server-side jika lolos bypass: *"Username/email dan password wajib diisi."* | Muncul peringatan pengisian form wajib. | **Valid** |
| **AUTH-07** | Keamanan Akun (*Account Lockout*) | Melakukan login gagal sebanyak 5 kali berturut-turut pada akun yang sama | Akun dikunci sementara (*locked*) selama 10 menit. Login selanjutnya (meskipun password benar) akan ditolak selama masa penguncian. | Akun terkunci dan menampilkan sisa waktu tunggu. | **Valid** |
| **AUTH-08** | Hak Akses (*RBAC Protection*) | Mengakses url `/admin/penduduk` langsung menggunakan browser dalam keadaan login sebagai role **Warga** | Sistem mendeteksi role Warga tidak memiliki hak akses (*unauthorized*), memblokir request, dan menampilkan halaman error 403 Forbidden atau me-redirect kembali ke dashboard warga. | Diarahkan ke halaman 403 Forbidden. | **Valid** |
| **AUTH-09** | Logout dari Sistem | Mengklik tombol "Logout" di sidebar/header | Sistem menghapus data session, meregenerasi ID session untuk keamanan, menghapus cookie remember-me (jika ada), dan me-redirect ke halaman login (`/login`). | Session hancur dan berhasil kembali ke halaman login. | **Valid** |

---

### Pengujian Modul 2: Kelola Data Penduduk (Admin)

Modul ini memvalidasi proses penginputan data kependudukan warga desa yang dilakukan oleh Staf Admin Desa.

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **PEND-01** | Tambah data penduduk dengan input valid | Isi lengkap: NIK (16 digit angka unik), No KK (16 digit), Nama, Tempat/Tgl Lahir, Jenis Kelamin, Agama, Alamat lengkap (RT/RW), dan No HP.<br>Klik "Simpan" | Sistem berhasil menyimpan data penduduk baru ke database dan menampilkan pesan sukses. | Data penduduk bertambah dan muncul pesan sukses. | **Valid** |
| **PEND-02** | Tambah penduduk dengan NIK duplikat | Mengisi form dengan NIK yang sudah terdaftar di sistem.<br>Klik "Simpan" | Sistem menolak pendaftaran dan memunculkan error validator: *"NIK sudah terdaftar."* | Pendaftaran ditolak dan data tidak tersimpan. | **Valid** |
| **PEND-03** | Tambah penduduk dengan format NIK tidak valid | Memasukkan NIK kurang dari 16 digit atau mengandung huruf (contoh: `12345ABC`). | Sistem menampilkan pesan validasi: *"NIK harus terdiri dari 16 digit angka."* | Sistem menampilkan pesan kesalahan format NIK. | **Valid** |
| **PEND-04** | Edit data penduduk | Memilih salah satu data penduduk, mengubah alamat dan status perkawinan, lalu mengklik "Perbarui". | Sistem memperbarui data penduduk di database dan mencatat riwayat perubahan di log aktivitas. | Data terupdate dan log tercatat. | **Valid** |
| **PEND-05** | Hapus (Soft Delete) data penduduk | Klik tombol "Hapus" pada salah satu data penduduk. | Data penduduk disembunyikan dari daftar aktif dengan mengisi kolom `deleted_at`, tetapi data tetap berada di database (*soft-deleting*). | Data hilang dari tabel utama penduduk namun tetap ada di DB dengan flag deleted. | **Valid** |

---

### Pengujian Modul 3: Pengajuan & Pelacakan Surat (Warga)

Modul ini memvalidasi alur pembuatan pengajuan surat oleh warga beserta pengunggahan lampiran yang diperlukan.

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **SURAT-01** | Membuka halaman buat pengajuan surat | Warga mengklik menu "Pengajuan Surat" -> "Buat Surat" | Sistem mendeteksi data NIK Warga yang login. Jika data NIK terdaftar, sistem menampilkan form pilihan jenis surat (DOM, SKU, SKTM, dll.) dan persyaratannya. | Form pengajuan surat terbuka beserta syarat berkas. | **Valid** |
| **SURAT-02** | Pengajuan surat tanpa profil penduduk terdaftar | Warga dengan akun yang belum dihubungkan ke data kependudukan mengklik "Buat Surat" | Sistem memblokir pengajuan dan menampilkan error: *"Data kependudukan Anda belum terdaftar. Silakan hubungi Staf Administrasi Desa."* | Di-redirect ke dashboard dengan pesan peringatan. | **Valid** |
| **SURAT-03** | Pengajuan surat dengan berkas valid | Memilih "Surat Keterangan Usaha", mengisi kolom Keperluan: "Pengajuan KUR BRI", mengunggah berkas pendukung (format PDF/JPG, ukuran < 2MB). Klik "Kirim". | Pengajuan tersimpan dengan status `menunggu`, nomor surat otomatis digenerate dengan format `SRT-YYYY-XXXXXX`, dan notifikasi dikirim ke admin. | Surat terbuat, berkas terunggah ke folder `uploads/lampiran/`, status `menunggu`. | **Valid** |
| **SURAT-04** | Pengisian pengajuan surat tanpa lampiran wajib | Mengirim form surat tanpa mengunggah file lampiran yang diwajibkan oleh jenis surat terpilih. | Sistem menolak pengajuan dan memberikan notifikasi berkas wajib belum diunggah. | Pengajuan ditolak dengan pesan peringatan berkas wajib. | **Valid** |
| **SURAT-05** | Upload berkas melebihi batas ukuran (Max Size) | Mengunggah file berkas syarat berukuran 5 MB (batas maksimal 2 MB). | Sistem membatalkan upload dan memberikan error: *"Ukuran file maksimal adalah 2MB."* | Upload gagal dan muncul pesan error ukuran berkas. | **Valid** |
| **SURAT-06** | Upload berkas dengan format dilarang (Extension Bypass) | Mengunggah berkas berekstensi `.php` or `.exe`. | Sistem memblokir file dan menampilkan error: *"Format berkas tidak diizinkan. Hanya menerima PDF, JPG, JPEG, PNG."* | File ditolak sistem. | **Valid** |
| **SURAT-07** | Pelacakan status surat (Tracking) | Warga masuk ke menu "Pelacakan Surat" | Sistem menampilkan linimasa status surat secara real-time (`menunggu` -> `diverifikasi` -> `diproses` -> `menunggu_persetujuan` -> `disetujui`/`selesai`). | Alur tracking status surat tampil akurat. | **Valid** |

---

### Pengujian Modul 4: Verifikasi & Persetujuan Surat (Admin & Kepala Desa)

Modul ini menguji proses verifikasi berkas pengajuan surat oleh Staf Admin, kemudian penandatanganan dan persetujuan akhir oleh Kepala Desa.

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **ADM-SRT-01** | Admin memeriksa berkas pengajuan | Admin masuk ke detail surat masuk, meninjau lampiran berkas yang diunggah warga. | Sistem menyediakan opsi bagi Admin untuk menandai lampiran sebagai valid/invalid dan mengubah status menjadi `diverifikasi` atau `ditolak`. | Berkas dapat diperiksa dan status berhasil diupdate. | **Valid** |
| **ADM-SRT-02** | Admin menolak pengajuan surat dengan alasan | Admin memilih opsi "Tolak Pengajuan", menulis catatan: *"Fotokopi KK buram/tidak terbaca"*. Klik "Kirim". | Status pengajuan berubah menjadi `ditolak`, kolom `catatan_admin` terisi, dan notifikasi penolakan dikirim ke akun warga terkait. | Status surat berganti menjadi `ditolak` dan warga menerima alasan penolakan. | **Valid** |
| **KADES-SRT-01** | Kepala Desa menyetujui pengajuan surat | Kades masuk ke daftar persetujuan surat, memeriksa draf surat, lalu mengklik "Setujui & Tanda Tangani". | Sistem membuat QR Code pengesahan (sebagai pengganti TTE), menggabungkannya ke dalam template PDF surat, merubah status surat menjadi `selesai`, dan mengirim notifikasi ke warga. | Surat berstatus `selesai` dan dokumen PDF ditandatangani digital siap unduh. | **Valid** |
| **KADES-SRT-02** | Warga mengunduh PDF surat yang disetujui | Warga mengklik tombol "Unduh Surat" pada surat berstatus selesai. | Browser berhasil mendownload file PDF resmi yang berisi detail surat dan QR Code verifikasi. | PDF surat terunduh dengan tata letak rapi beserta QR Code. | **Valid** |

---

### Pengujian Modul 5: Pengaduan Masyarakat & Integrasi Deteksi AI YOLOv8

Modul ini menguji pembuatan pengaduan oleh warga, proses pengunggahan foto infrastruktur rusak (misal: jalan berlubang/rusak, tumpukan sampah, banjir), dan pengolahan gambar otomatis oleh AI Server menggunakan YOLOv8 untuk merekomendasikan kategori kerusakan dan prioritas penanganan.

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **ADU-01** | Membuat pengaduan tanpa menyertakan foto | Mengisi judul pengaduan, kategori: `jalan_rusak`, deskripsi: `Jalan depan RT 02 berlubang parah`, alamat lokasi. Tidak mengunggah foto. Klik "Kirim". | Sistem berhasil menyimpan pengaduan tanpa analisis AI visual. Status pengaduan `menunggu`. Prioritas default: `sedang`. | Pengaduan tersimpan dalam database dengan status menunggu. | **Valid** |
| **ADU-02** | Membuat pengaduan dengan unggahan foto (Integrasi AI YOLOv8) | Mengisi data pengaduan dan mengunggah foto jalan rusak/sampah menumpuk. Klik "Kirim". | 1. Sistem menyimpan pengaduan.<br>2. Sistem mengirim foto ke API AI Server (`http://127.0.0.1:8000/detect`).<br>3. AI mendeteksi objek (misal: `pothole` atau `garbage`) dengan confidence score.<br>4. AI mengirim respons JSON berisi koordinat bounding box, label kelas, dan rekomendasi prioritas.<br>5. Database mencatat hasil di tabel `hasil_ai` dan sistem mengupdate kolom `prioritas` pengaduan menjadi `tinggi`/`kritis` jika terdeteksi kerusakan berat secara otomatis. | Foto teranalisis oleh AI server, bounding box tersimpan, prioritas pengaduan disesuaikan otomatis berdasarkan tingkat kerusakan yang dideteksi. | **Valid** |
| **ADU-03** | Penanganan kegagalan koneksi AI Server (Fallback Mechanism) | Mengunggah pengaduan dengan gambar saat Python AI Server dinonaktifkan. | Sistem PHP mendeteksi *timeout* atau kegagalan koneksi ke API AI Server, secara anggun (*gracefully fallback*) melewati proses deteksi AI, menyimpan pengaduan warga dengan normal tanpa menyebabkan halaman crash (500 Error). | Pengaduan tetap tersimpan sukses dengan status normal, log server mencatat kegagalan API. | **Valid** |
| **ADU-04** | Admin menanggapi dan memproses pengaduan | Admin membuka detail pengaduan, melihat peta lokasi (latitude & longitude), melihat analisis foto AI dengan overlay bounding box, menulis tanggapan penanganan, dan merubah status menjadi `diproses`. | Status berubah menjadi `diproses`, tanggal tindak lanjut tercatat, notifikasi dikirim ke pelapor warga. | Tanggapan terkirim dan status pengaduan diperbarui. | **Valid** |
| **ADU-05** | Menyelesaikan pengaduan dan memberi rating | Admin menandai pengaduan selesai. Warga masuk ke detail pengaduan selesai, lalu memberikan penilaian bintang 5 dan ulasan kepuasan. | Pengaduan berubah status menjadi `selesai`. Rating kepuasan disimpan pada tabel `pengaduan` untuk bahan evaluasi kinerja. | Status pengaduan menjadi selesai dan rating berhasil tersimpan. | **Valid** |

---

### Pengujian Modul 6: Chatbot AI Asisten Desa (Warga)

Modul ini menguji percakapan dua arah antara warga dengan asisten AI yang ditenagai oleh model Gemini API untuk menjawab pertanyaan seputar layanan administrasi desa.

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **CHAT-01** | Mengirimkan pesan pembuka ke Chatbot | Warga mengetik pesan: *"Bagaimana syarat membuat Surat Keterangan Usaha (SKU)?"* lalu klik kirim. | 1. Pesan dikirim ke database untuk mencatat chat history.<br>2. Request dikirim ke Python AI Server yang mengakses Gemini API dengan system prompt regulasi desa.<br>3. AI merespons dengan bahasa yang sopan dan memberikan informasi syarat SKU secara tepat berdasarkan basis pengetahuan desa.<br>4. Jawaban dirender secara real-time menggunakan AJAX/Stream di halaman chat. | Chatbot merespons dengan daftar persyaratan SKU yang sesuai dengan data sistem desa. | **Valid** |
| **CHAT-02** | Mempertahankan riwayat percakapan (*Context Retention*) | Mengirimkan pesan lanjutan: *"Berapa hari proses pembuatannya?"* | Chatbot memahami konteks "pembuatan" merujuk pada SKU (dari chat sebelumnya) dan menjawab: *"Proses pembuatan SKU di SIAP-Desa estimasinya adalah 3 hari kerja."* | Chatbot merespons secara koheren berdasarkan konteks sebelumnya. | **Valid** |
| **CHAT-03** | Memberikan rating pada percakapan chatbot | Klik tombol jempol ke atas/ke bawah (like/dislike) atau memberi rating bintang setelah sesi percakapan selesai. | Sistem menyimpan feedback rating warga ke tabel `chat_rating` beserta alasan ulasannya. | Feedback rating terekam di database admin. | **Valid** |

---

### Pengujian Modul 7: Laporan & AI Analytics (Kepala Desa & Admin)

Modul ini memvalidasi pembuatan dokumen laporan dan tampilan statistik analitis berbasis AI untuk membantu pengambilan keputusan oleh Kepala Desa.

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **REP-01** | Pembuatan Laporan Bulanan Administrasi | Admin masuk ke menu "Laporan", memilih tipe laporan: `Surat Pengajuan`, menentukan rentang tanggal, klik "Generate Laporan". | Sistem menarik data dari database, menghitung total pengajuan disetujui/ditolak, membuat dokumen ringkasan dalam format tabel dan memunculkan tombol "Cetak PDF" / "Ekspor Excel". | Dokumen laporan bulanan berhasil dibuat dan diekspor dengan data akurat. | **Valid** |
| **REP-02** | Dashboard AI Analytics Kepala Desa | Kepala Desa masuk ke menu "AI Analytics" | Sistem menampilkan grafik tren pengaduan (kategori terbanyak, lokasi rawan kerusakan), rata-rata rating kepuasan pelayanan, serta rangkuman teks rekomendasi dari AI (Gemini) mengenai prioritas alokasi anggaran infrastruktur bulan depan berdasarkan data aduan warga. | Grafik analitis dan ringkasan teks rekomendasi AI tampil dinamis di layar Kepala Desa. | **Valid** |

---

### Pengujian Modul 8: Kelola Bantuan Sosial (Admin & Warga)

Modul ini memvalidasi pendaftaran program bantuan sosial (BLT, PKH, dll.) serta penilaian kelayakan penerima bantuan.

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **BANSOS-01**| Tambah Program Bantuan Sosial Baru | Admin membuat program bansos baru: "BLT Kemiskinan Ekstrem 2026", mengisi kuota, kriteria penerima (misal: pekerjaan buruh, pendapatan < Rp1.500.000, status rumah sewa). | Program bansos baru berhasil disimpan dan muncul di halaman warga. | Program bansos terbit dengan kriteria dan detail lengkap. | **Valid** |
| **BANSOS-02**| Pengecekan Penerima secara Otomatis | Klik tombol "Analisis Kelayakan Calon Penerima Bansos". | Sistem melakukan filter otomatis mencocokkan data profil ekonomi penduduk dengan kriteria program bantuan menggunakan query database terpadu. | Sistem menampilkan rekomendasi daftar nama warga yang layak menerima bansos berdasarkan kriteria. | **Valid** |

---

### Pengujian Modul 9: Manajemen User & Sistem Backup (Superadmin)

Modul ini memvalidasi fungsi pemeliharaan sistem, pembuatan akun staf baru, pemantauan log aktivitas, dan pencadangan database oleh Superadmin.

| ID | Skenario Pengujian | Langkah/Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|:---|:---|:---|:---|:---|:---:|
| **SADM-01** | Membuat akun staf admin baru | Superadmin masuk menu "Kelola User", memasukkan username, email, password, memilih role `Admin Desa`, klik "Simpan". | Akun admin baru berhasil dibuat dengan enkripsi password menggunakan BCRYPT (cost=12), dan akun dapat langsung digunakan untuk login. | Akun tercipta dengan aman dan fungsional. | **Valid** |
| **SADM-02** | Penonaktifan Akun User (Suspended Account) | Mengubah status akun `warga` menjadi tidak aktif (`is_active = 0`) lewat menu edit user. | Akun dinonaktifkan. Saat warga bersangkutan mencoba login kembali, sistem memblokir akses dan memberi pesan: *"Akun Anda ditangguhkan/tidak aktif. Hubungi Superadmin."* | Login ditolak untuk akun non-aktif dengan notifikasi yang sesuai. | **Valid** |
| **SADM-03** | Pemantauan Audit Log Aktivitas | Superadmin membuka halaman "Log Aktivitas". | Sistem menampilkan rekaman jejak aktivitas user secara detail (nama user, modul, aksi yang dilakukan, data sebelum & sesudah diubah, alamat IP, dan User Agent). | Tabel log aktivitas menampilkan data audit trail yang lengkap. | **Valid** |
| **SADM-04** | Backup Database Sistem | Superadmin mengklik tombol "Backup Database". | Sistem menjalankan perintah backup database MySQL (`mysqldump`), mengompres file menjadi format `.sql.gz`, menyimpannya di folder `storage/backups/`, dan mengunduhnya secara otomatis ke komputer. | File backup terbuat dan terunduh dengan sukses. | **Valid** |

---

### Kesimpulan Hasil Pengujian
Berdasarkan hasil pengujian di atas menggunakan metode pengujian **Black Box**, dapat disimpulkan bahwa:
1. Seluruh fungsi inti pada modul **Autentikasi (RBAC)**, **Manajemen Penduduk**, **Pengajuan Surat**, dan **Persetujuan Surat** telah berjalan sesuai dengan spesifikasi fungsionalitas yang diharapkan.
2. Integrasi **AI Server (YOLOv8 & Gemini API)** untuk analisis pengaduan gambar dan asisten chatbot warga berjalan dengan baik dengan penanganan error (*fallback mechanism*) yang aman jika server AI mengalami kendala koneksi.
3. Fungsi administratif tingkat lanjut seperti **Laporan**, **AI Analytics**, **Kelola Bantuan Sosial**, dan **Manajemen Superadmin** lolos uji validasi dengan status **Valid (Sesuai)**.

*Dokumen ini dibuat secara otomatis sebagai berkas pelaporan pengujian sistem.*
