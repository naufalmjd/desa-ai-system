<?php $pageTitle = 'Buat Pengaduan'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Form Pengaduan -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-megaphone-fill me-2 text-warning"></i>Form Pengaduan Masyarakat
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/warga/pengaduan/store"
                      id="pengaduanForm" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Judul Pengaduan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control"
                               placeholder="Ringkasan singkat masalah" maxlength="200" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="">— Pilih Kategori —</option>
                            <?php foreach([
                                'jalan_rusak'=>'Jalan Rusak','sampah'=>'Sampah','banjir'=>'Banjir',
                                'lampu_mati'=>'Lampu Jalan Mati','pohon_tumbang'=>'Pohon Tumbang',
                                'infrastruktur'=>'Infrastruktur Lain','lainnya'=>'Lainnya'
                            ] as $val=>$label): ?>
                            <option value="<?= $val ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi Detail <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" rows="4"
                                  placeholder="Jelaskan masalah secara rinci..." required maxlength="2000"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lokasi / Alamat <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_alamat" class="form-control"
                               placeholder="Nama jalan, RT/RW, atau landmark terdekat" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Koordinat GPS</label>
                        <div class="input-group">
                            <input type="text" id="koordinat" class="form-control font-monospace"
                                   placeholder="Klik tombol untuk deteksi otomatis" readonly>
                            <input type="hidden" name="latitude"  id="lat">
                            <input type="hidden" name="longitude" id="lng">
                            <button type="button" class="btn btn-outline-primary" onclick="getLocation()">
                                <i class="bi bi-geo-alt-fill"></i> Deteksi
                            </button>
                        </div>
                        <div id="miniMap" class="rounded-3 mt-2" style="height:180px;display:none"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Foto Bukti</label>
                        <div class="border-2 border-dashed rounded-3 p-4 text-center bg-light"
                             id="dropZone" style="cursor:pointer;transition:.2s"
                             onclick="document.getElementById('fotoInput').click()">
                            <i class="bi bi-cloud-upload fs-2 text-muted mb-2 d-block"></i>
                            <p class="text-muted small mb-1">Klik atau drag & drop foto</p>
                            <small class="text-muted">JPG, PNG, WebP — Maks 5MB</small>
                            <div id="previewContainer" class="mt-3 d-none">
                                <img id="previewImg" class="rounded-3" style="max-height:200px;max-width:100%">
                            </div>
                        </div>
                        <input type="file" name="foto" id="fotoInput" accept="image/*"
                               class="d-none" onchange="previewFoto(this)">
                    </div>

                    <button type="submit" class="btn btn-warning fw-bold w-100" id="submitBtn">
                        <i class="bi bi-send-fill me-2"></i>Kirim Pengaduan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- AI Panel -->
    <div class="col-lg-5">
        <div class="card mb-3" id="aiPanel">
            <div class="card-header bg-white py-3 d-flex align-items-center gap-2">
                <div class="bg-success bg-opacity-10 rounded-2 p-1">
                    <i class="bi bi-lightning-charge-fill text-success"></i>
                </div>
                <span>AI Computer Vision</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle ms-auto" style="font-size:.65rem">YOLOv8</span>
            </div>
            <div class="card-body">
                <div id="aiDefault">
                    <p class="text-muted small mb-3">
                        Upload foto untuk analisis AI otomatis. Sistem akan mendeteksi
                        jenis masalah dan menentukan prioritas penanganan.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach(['Jalan Rusak','Sampah','Banjir','Lampu Mati','Pohon Tumbang'] as $cat): ?>
                        <span class="badge bg-light border text-muted" style="font-size:.7rem"><?= $cat ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Loading -->
                <div id="aiLoading" class="d-none text-center py-4">
                    <div class="spinner-border text-success mb-3" role="status"></div>
                    <p class="text-muted small">YOLOv8 menganalisis gambar...</p>
                    <div class="text-start">
                        <?php foreach(['Object Detection','Classification','Confidence Scoring','Priority Assessment'] as $step): ?>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
                            <small class="text-success"><?= $step ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Result -->
                <div id="aiResult" class="d-none">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span class="fw-semibold text-success small">Hasil Analisis AI</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless" style="font-size:.8rem">
                            <tr><td class="text-muted">Kategori Terdeteksi</td>
                                <td class="fw-bold" id="aiKategori">—</td></tr>
                            <tr><td class="text-muted">Confidence Score</td>
                                <td class="fw-bold text-success" id="aiConfidence">—</td></tr>
                            <tr><td class="text-muted">Prioritas AI</td>
                                <td id="aiPrioritas">—</td></tr>
                            <tr><td class="text-muted">Model</td>
                                <td class="font-monospace" style="font-size:.7rem">YOLOv8n + OpenCV</td></tr>
                        </table>
                    </div>
                    <div id="aiProgressBar" class="mb-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Confidence</span>
                            <span id="aiPct" class="fw-bold">0%</span>
                        </div>
                        <div class="progress" style="height:6px">
                            <div class="progress-bar bg-success" id="aiBar" style="width:0%"></div>
                        </div>
                    </div>
                    <div id="aiLabels" class="mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Panduan -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-info-circle me-2 text-primary"></i>Panduan Pengaduan
            </div>
            <div class="card-body">
                <ol class="ps-3" style="font-size:.82rem;color:#374151">
                    <li class="mb-2">Pilih kategori yang sesuai dengan masalah</li>
                    <li class="mb-2">Jelaskan masalah secara detail dan jelas</li>
                    <li class="mb-2">Sertakan lokasi yang tepat (GPS sangat membantu)</li>
                    <li class="mb-2">Upload foto sebagai bukti (AI akan menganalisis otomatis)</li>
                    <li class="mb-0">Pantau status pengaduan di halaman Riwayat</li>
                </ol>
                <div class="mt-3 p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25">
                    <small class="text-success">
                        <i class="bi bi-lightning-charge me-1"></i>
                        <strong>AI YOLOv8</strong> akan otomatis mengklasifikasikan foto dan menentukan prioritas penanganan.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$headExtra = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">';
$footerExtra = <<<JS
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, marker;

function getLocation() {
    if (!navigator.geolocation) {
        alert('Geolokasi tidak didukung browser Anda.');
        return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
        const lat = pos.coords.latitude.toFixed(6);
        const lng = pos.coords.longitude.toFixed(6);
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        document.getElementById('koordinat').value = lat + ', ' + lng;

        const mapEl = document.getElementById('miniMap');
        mapEl.style.display = 'block';
        if (!map) {
            map = L.map('miniMap').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        }
        if (marker) marker.remove();
        marker = L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Anda').openPopup();
        map.setView([lat, lng], 16);
    }, err => {
        alert('Gagal mendapatkan lokasi: ' + err.message);
    });
}

function previewFoto(input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('previewContainer').classList.remove('d-none');
    };
    reader.readAsDataURL(file);

    // Jalankan AI detection
    runAiDetect(file);
}

function runAiDetect(file) {
    document.getElementById('aiDefault').classList.add('d-none');
    document.getElementById('aiResult').classList.add('d-none');
    document.getElementById('aiLoading').classList.remove('d-none');

    const formData = new FormData();
    formData.append('foto', file);
    formData.append('_csrf_token', CSRF_TOKEN);

    fetch(APP_URL + '/warga/pengaduan/analyze', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        document.getElementById('aiLoading').classList.add('d-none');
        document.getElementById('aiResult').classList.remove('d-none');

        if (res.success && res.data) {
            const d = res.data;
            document.getElementById('aiKategori').textContent   = d.kategori || '—';
            document.getElementById('aiConfidence').textContent  = d.confidence + '%';
            document.getElementById('aiPct').textContent         = d.confidence + '%';
            document.getElementById('aiBar').style.width         = d.confidence + '%';

            const priColors = { kritis:'danger', tinggi:'warning', sedang:'primary', rendah:'success' };
            const pri = d.prioritas || 'sedang';
            document.getElementById('aiPrioritas').innerHTML =
                `<span class="badge bg-\${priColors[pri]}-subtle text-\${priColors[pri]} border border-\${priColors[pri]}-subtle">\${pri.toUpperCase()}</span>`;

            if (d.labels?.length) {
                document.getElementById('aiLabels').innerHTML =
                    '<small class="text-muted d-block mb-1">Objek terdeteksi:</small>' +
                    d.labels.map(l => `<span class="badge bg-light border me-1" style="font-size:.65rem">\${l}</span>`).join('');
            }

            // Auto-set kategori form jika terdeteksi
            const catMap = { 'Jalan Rusak':'jalan_rusak','Sampah':'sampah','Banjir':'banjir',
                             'Lampu Jalan Mati':'lampu_mati','Pohon Tumbang':'pohon_tumbang' };
            const formCat = catMap[d.kategori];
            if (formCat) document.querySelector('[name=kategori]').value = formCat;
        } else {
            document.getElementById('aiKategori').textContent = 'Tidak terdeteksi';
        }
    })
    .catch(() => {
        document.getElementById('aiLoading').classList.add('d-none');
        document.getElementById('aiDefault').classList.remove('d-none');
    });
}

// Drag & drop
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-primary'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-primary'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('border-primary');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        document.getElementById('fotoInput').files = e.dataTransfer.files;
        previewFoto(document.getElementById('fotoInput'));
    }
});

document.getElementById('pengaduanForm').addEventListener('submit', () => {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
    btn.disabled = true;
});
</script>
JS;
?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
