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
    const routePage = () => {
        // Ambil hash URL, default ke '#landing'
        const hash = window.location.hash || '#landing';
        const pageId = hash.substring(1);
        
        // Hide semua section
        document.querySelectorAll('.page-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Tampilkan section target
        const activeSection = document.getElementById(pageId);
        if (activeSection) {
            activeSection.classList.add('active');
            window.scrollTo(0, 0);
        } else {
            // Fallback jika page tidak ditemukan
            document.getElementById('landing').classList.add('active');
        }
        
        // Update status active pada link navbar
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

// --- Render Content ---
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

// --- Format Tanggal Indonesia ---
function formatDate(dateStr) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateStr).toLocaleDateString('id-ID', options);
}

// --- View News Detail (Modal/Popup) ---
function viewNewsDetail(newsId) {
    const news = MOCK_BERITA.find(b => b.id === newsId);
    if (!news) return;

    // Set Modal Content
    document.getElementById('newsDetailModalLabel').innerText = news.title;
    document.getElementById('modal-news-date').innerHTML = `<i class="bi bi-calendar3 me-1"></i> ${formatDate(news.date)}`;
    document.getElementById('modal-news-img').src = news.image;
    document.getElementById('modal-news-body').innerHTML = `<p style="line-height: 1.7; font-size: 0.92rem;">${news.content}</p>`;

    // Render attachments
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

    // Tampilkan Modal
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
    
    const adminPhone = "628123456789"; // Placeholder no admin
    
    // Buat template pesan teks
    const textMsg = `Halo Admin DigitalDesa, saya memerlukan bantuan AMBULANS SEGERA.

Nama Pelapor: ${nama}
Lokasi Penjemputan: ${alamat}
Kondisi Darurat: ${kondisi}

Mohon admin segera mencarikan sopir ambulans desa yang sedang ready/siap bertugas.`;
    
    // URL Encode
    const url = `https://api.whatsapp.com/send?phone=${adminPhone}&text=${encodeURIComponent(textMsg)}`;
    
    // Tampilkan notifikasi lalu buka tautan
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

// --- Contact Form Handling ---
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

// --- Login Simulation ---
function handleLoginSubmit(event) {
    event.preventDefault();
    const user = document.getElementById('l-user').value.trim();
    const pass = document.getElementById('l-pass').value;

    if (!user || !pass) {
        Swal.fire('Oops!', 'Masukkan email/username dan kata sandi.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Masuk Berhasil!',
        text: `Selamat datang kembali, ${user}. Anda masuk sebagai Administrator Desa.`,
        icon: 'success',
        confirmButtonColor: '#6366f1'
    }).then(() => {
        // Kembalikan ke beranda setelah login sukses
        window.location.hash = "#beranda";
        document.getElementById('form-login').reset();
    });
}

// --- Theme Switcher ---
function initTheme() {
    const btn = document.getElementById('theme-toggle-btn');
    const icon = document.getElementById('theme-icon');
    const currentTheme = localStorage.getItem('dd_theme') || 'dark';

    // Terapkan tema awal
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
    
    // Bind Submit Events
    document.getElementById('form-ambulans')?.addEventListener('submit', handleAmbulanceOrder);
    document.getElementById('form-kontak')?.addEventListener('submit', handleContactSubmit);
    document.getElementById('form-login')?.addEventListener('submit', handleLoginSubmit);
});
