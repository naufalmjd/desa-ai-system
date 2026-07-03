<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Surat - <?= htmlspecialchars($surat['nomor']) ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    @media screen {
        body { background: #e0e0e0; padding: 30px 0; }
        .sheet {
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 4px;
            box-sizing: border-box;
            width: 21cm;
            min-height: 29.7cm;
            padding: 2.5cm 2cm;
            margin: 0 auto;
        }
        .print-btn-container {
            width: 21cm;
            margin: 0 auto 20px auto;
            text-align: right;
        }
    }
    @media print {
        body { background: white; padding: 0; }
        .sheet {
            width: 100%;
            padding: 0;
            margin: 0;
            border: none;
            box-shadow: none;
        }
        .no-print { display: none !important; }
    }
    
    body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
    
    /* Kop Surat */
    .kop-surat {
        text-align: center;
        border-bottom: 4px double #000;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    .kop-logo {
        width: 80px;
        height: auto;
        float: left;
        margin-right: -80px; /* overlay text */
    }
    .kop-header {
        margin-left: 80px;
        margin-right: 80px;
    }
    .kop-header h4 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
    .kop-header h3 { margin: 2px 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
    .kop-header h2 { margin: 2px 0; font-size: 18pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .kop-header p { margin: 0; font-size: 10pt; font-style: italic; }
    
    /* Content */
    .surat-title-container {
        text-align: center;
        margin-bottom: 30px;
    }
    .surat-title {
        font-size: 14pt;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 2px;
        text-transform: uppercase;
    }
    .surat-nomor {
        font-size: 11pt;
        font-family: monospace;
    }
    .surat-body {
        text-align: justify;
        text-justify: inter-word;
        margin-bottom: 40px;
    }
    .surat-body p { margin-bottom: 15px; text-indent: 1cm; }
    
    /* TTD Section */
    .ttd-section {
        float: right;
        text-align: center;
        width: 300px;
        page-break-inside: avoid;
    }
    .ttd-date { margin-bottom: 10px; }
    .ttd-jabatan { font-weight: bold; margin-bottom: 15px; }
    .ttd-qr {
        display: inline-block;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fff;
        margin-bottom: 15px;
    }
    .ttd-nama { font-weight: bold; text-decoration: underline; text-transform: uppercase; }
    .ttd-nip { font-size: 10pt; }
    
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }
</style>
</head>
<body>

<div class="print-btn-container no-print">
    <button onclick="window.print()" class="btn btn-primary shadow-sm fw-bold">
        <i class="bi bi-printer-fill me-1"></i> Cetak Surat / Simpan PDF
    </button>
    <button onclick="window.close()" class="btn btn-light border shadow-sm">Tutup</button>
</div>

<div class="sheet clearfix">
    <!-- Kop Surat -->
    <div class="kop-surat clearfix">
        <img src="https://upload.wikimedia.org/wikipedia/commons/d/d2/Lambang_Kabupaten_Malang.svg" alt="Logo Kabupaten Malang" class="kop-logo" style="max-height: 90px;">
        <div class="kop-header">
            <h4>Pemerintah Kabupaten Malang</h4>
            <h3>Kecamatan Tumpang</h3>
            <h2>Kantor Kepala Desa Sukamaju</h2>
            <p>Jalan Raya Sukamaju No. 12, Tumpang, Malang 65156 | Email: pemdes@sukamaju.desa.id</p>
        </div>
    </div>
    
    <!-- Title & Nomor -->
    <?php
    // Extract title from template body if it contains custom title, or generate default
    $isi = htmlspecialchars_decode($surat['isi_surat'] ?? '');
    
    // Fallback if empty
    if (!$isi) {
        $isi = "SURAT KETERANGAN\n\nYang bertanda tangan di bawah ini Kepala Desa Sukamaju menerangkan bahwa...";
    }
    
    // Replace {nomor_surat} with actual number in the body
    $isi = str_replace('{nomor_surat}', $surat['nomor'], $isi);
    
    // Separate title if first line is title
    $lines = explode("\n", $isi);
    $firstLine = trim($lines[0]);
    
    $title = $surat['jenis_nama'];
    $bodyText = $isi;
    
    // If the first line is capitalized and looks like a title, use it
    if (strlen($firstLine) > 5 && strtoupper($firstLine) === $firstLine) {
        $title = $firstLine;
        array_shift($lines);
        $bodyText = implode("\n", $lines);
    }
    ?>
    
    <div class="surat-title-container">
        <div class="surat-title"><?= htmlspecialchars($title) ?></div>
        <div class="surat-nomor">Nomor: <?= htmlspecialchars($surat['nomor']) ?></div>
    </div>
    
    <!-- Body -->
    <div class="surat-body">
        <?= nl2br(htmlspecialchars($bodyText)) ?>
    </div>
    
    <!-- Signature -->
    <div class="ttd-section">
        <div class="ttd-date">Sukamaju, <?= date('d F Y', strtotime($surat['approved_at'] ?? $surat['created_at'])) ?></div>
        <div class="ttd-jabatan">Kepala Desa Sukamaju</div>
        
        <?php if ($surat['qr_code']): ?>
        <div class="ttd-qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode(APP_URL . '/warga/surat/show/' . $surat['id']) ?>" alt="QR Code Signature" style="width: 110px; height: 110px;">
        </div>
        <?php else: ?>
        <div style="height: 100px;" class="no-print text-muted d-flex align-items-center justify-content-center border border-dashed rounded mb-2">
            Belum ditandatangani
        </div>
        <?php endif; ?>
        
        <div class="ttd-nama">H. ACHMAD FAUZI, S.IP</div>
        <div class="ttd-nip">NIP. 19740822 200212 1 002</div>
    </div>
</div>

<script>
    window.onload = function() {
        // Automatically open print dialog if status is selesai
        <?php if ($surat['status'] === 'selesai'): ?>
        setTimeout(function() {
            window.print();
        }, 500);
        <?php endif; ?>
    }
</script>
</body>
</html>
