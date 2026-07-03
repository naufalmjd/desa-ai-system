<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Bulanan — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Times New Roman', sans-serif;
            background-color: #ffffff;
            color: #000000;
            padding: 30px;
            font-size: 13px;
        }
        .kop-surat {
            border-bottom: 3px double #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-subtitle {
            font-size: 12px;
            color: #555;
        }
        .report-title {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        .report-date {
            text-align: center;
            font-size: 12px;
            color: #444;
            margin-bottom: 25px;
        }
        .table-stats th {
            background-color: #f2f2f2 !important;
            font-weight: 700;
            text-align: center;
        }
        .table-data th {
            background-color: #f8f9fa !important;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        .table-data td {
            font-size: 11.5px;
        }
        .signature-box {
            margin-top: 40px;
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 75px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar for manual print trigger -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-2 bg-light border rounded no-print">
        <span class="text-secondary small"><i class="bi bi-info-circle me-1"></i> Gunakan opsi <b>Save as PDF</b> pada dialog cetak browser Anda untuk menyimpan sebagai berkas PDF.</span>
        <div>
            <button onclick="window.close()" class="btn btn-sm btn-secondary me-2">Tutup Halaman</button>
            <button onclick="window.print()" class="btn btn-sm btn-primary">Cetak Sekarang</button>
        </div>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat d-flex align-items-center gap-3">
        <div style="flex: 1; text-align: center;">
            <div class="kop-title">Pemerintah Kabupaten Bogor</div>
            <div class="kop-title">Kecamatan Cisarua</div>
            <div class="kop-title" style="font-size: 18px;">Kantor Kepala Desa Sukamaju</div>
            <div class="kop-subtitle">Jl. Raya Puncak No. 123, Sukamaju, Cisarua, Bogor, Jawa Barat 16750</div>
            <div class="kop-subtitle">Email: info@sukamaju-bogor.desa.id | Telp: (0251) 825XXXX</div>
        </div>
    </div>

    <!-- Judul Laporan -->
    <div class="report-title">Laporan Kinerja Bulanan Pelayanan Warga</div>
    <div class="report-date">Periode: <?= date('F Y') ?></div>

    <!-- Ringkasan Statistik -->
    <h6 class="fw-bold mb-2">I. RINGKASAN VOLUME PELAYANAN</h6>
    <table class="table table-bordered table-stats mb-4">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori Pelayanan</th>
                <th>Total Pengajuan</th>
                <th>Status Selesai</th>
                <th>Tingkat Penyelesaian (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Pengajuan Surat Administrasi Warga</td>
                <td class="text-center"><?= $stats['total_surat'] ?></td>
                <td class="text-center"><?= $stats['surat_selesai'] ?></td>
                <td class="text-center fw-bold"><?= $stats['total_surat'] > 0 ? round(($stats['surat_selesai'] / $stats['total_surat']) * 100, 1) : 0 ?>%</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Aduan / Pengaduan Warga</td>
                <td class="text-center"><?= $stats['total_pengaduan'] ?></td>
                <td class="text-center"><?= $stats['pengaduan_selesai'] ?></td>
                <td class="text-center fw-bold"><?= $stats['total_pengaduan'] > 0 ? round(($stats['pengaduan_selesai'] / $stats['total_pengaduan']) * 100, 1) : 0 ?>%</td>
            </tr>
        </tbody>
    </table>

    <!-- Rincian Surat -->
    <h6 class="fw-bold mb-2">II. DAFTAR PENGAJUAN SURAT ADMINISTRASI (TERBARU)</h6>
    <table class="table table-bordered table-striped table-data mb-4">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 25%;">Jenis Surat</th>
                <th style="width: 25%;">Pemohon</th>
                <th style="width: 20%;" class="text-center">Tanggal Pengajuan</th>
                <th style="width: 25%;" class="text-center">Status Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($suratList as $s): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($s['jenis_nama']) ?></td>
                <td><?= htmlspecialchars($s['pemohon_nama']) ?></td>
                <td class="text-center"><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                <td class="text-center fw-semibold text-uppercase"><?= htmlspecialchars($s['status']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($suratList)): ?>
            <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data pengajuan surat</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Rincian Aduan -->
    <h6 class="fw-bold mb-2">III. DAFTAR ADUAN & MASUKAN WARGA (TERBARU)</h6>
    <table class="table table-bordered table-striped table-data mb-4">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 20%;">Pelapor</th>
                <th style="width: 35%;">Judul Laporan</th>
                <th style="width: 20%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($pengaduanList as $p): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($p['kategori']) ?></td>
                <td><?= htmlspecialchars($p['pelapor_nama']) ?></td>
                <td><?= htmlspecialchars($p['judul']) ?></td>
                <td class="text-center fw-semibold text-uppercase"><?= htmlspecialchars($p['status']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pengaduanList)): ?>
            <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data aduan warga</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Penutup & Tanda Tangan -->
    <div class="w-100 clearfix">
        <div class="signature-box">
            <div>Sukamaju, <?= date('d F Y') ?></div>
            <div class="fw-bold">Mengetahui,</div>
            <div class="fw-semibold">Admin Pelayanan Desa Sukamaju</div>
            <div class="signature-space"></div>
            <div class="fw-bold" style="text-decoration: underline;"><?= htmlspecialchars($user['nama']) ?></div>
            <div style="font-size: 11px; color: #555;">NIP/ID. <?= str_pad((string)$user['id'], 5, '0', STR_PAD_LEFT) ?></div>
        </div>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 800);
        };
    </script>
</body>
</html>
