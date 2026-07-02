<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>403 — Akses Ditolak</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>* { font-family: 'Plus Jakarta Sans', sans-serif; } body { background: #eef2f7; }</style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
<div class="text-center" style="max-width:480px">
    <div class="bg-danger bg-opacity-10 rounded-4 d-inline-flex p-4 mb-4">
        <i class="bi bi-shield-x text-danger" style="font-size:4rem"></i>
    </div>
    <h1 class="fw-black text-danger mb-2">403</h1>
    <h3 class="fw-bold text-dark mb-3">Akses Ditolak</h3>
    <p class="text-muted mb-4">
        Anda tidak memiliki hak akses ke halaman ini.<br>
        Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
    </p>
    <div class="d-flex gap-3 justify-content-center">
        <button onclick="history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </button>
        <a href="<?= defined('APP_URL') ? APP_URL : '/' ?>/login" class="btn btn-danger">
            <i class="bi bi-house me-2"></i>Ke Login
        </a>
    </div>
    <?php if (!empty($redirectUrl)): ?>
    <div class="mt-4 p-3 bg-white rounded-3 border text-start" style="font-size:.8rem">
        <p class="text-muted mb-1">Dashboard yang sesuai dengan role Anda:</p>
        <a href="<?= htmlspecialchars($redirectUrl) ?>" class="btn btn-sm btn-primary">
            Buka Dashboard Saya <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
