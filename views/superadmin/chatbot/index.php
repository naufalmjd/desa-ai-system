<?php $pageTitle = 'Log Chatbot AI'; ?>

<?php ob_start(); ?>

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex gap-2 rounded-3 mb-4 border-0 text-white" style="background: rgba(var(--bs-<?= $flash['type'] ?>-rgb), 0.2); backdrop-filter: blur(10px);" role="alert">
    <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Chat Sessions Column -->
    <div class="col-lg-7">
        <div class="card border-0 h-100">
            <div class="card-header bg-transparent py-3 border-0">
                <span class="fs-6 fw-bold text-white"><i class="bi bi-chat-left-text me-2 text-primary"></i>Sesi Percakapan Pengguna</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Pengguna</th>
                                <th>Jumlah Pesan</th>
                                <th>Waktu Mulai</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessions as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary fw-bold" style="width: 28px; height: 28px; font-size: 0.7rem; color: #fff !important;">
                                            <?= strtoupper(substr($s['username'], 0, 2)) ?>
                                        </div>
                                        <span class="fw-semibold text-white"><?= htmlspecialchars($s['username']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge text-bg-secondary bg-opacity-25 text-white-50" style="font-size: 0.8rem;"><?= $s['message_count'] ?> pesan</span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($s['started_at'])) ?></small>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= APP_URL ?>/superadmin/chatbot/session/<?= $s['session_id'] ?>" class="btn btn-sm btn-primary px-3" style="border-radius: 8px;">
                                        <i class="bi bi-eye me-1"></i> Detail Chat
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($sessions)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">Belum ada percakapan chatbot.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Feedback & Ratings Column -->
    <div class="col-lg-5">
        <div class="card border-0 h-100">
            <div class="card-header bg-transparent py-3 border-0">
                <span class="fs-6 fw-bold text-white"><i class="bi bi-star-fill me-2 text-warning"></i>Rating & Ulasan Pengguna</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">User</th>
                                <th>Rating</th>
                                <th>Komentar</th>
                                <th class="pe-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ratings as $r): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold text-white d-block" style="font-size: 0.85rem;"><?= htmlspecialchars($r['username']) ?></span>
                                </td>
                                <td>
                                    <div class="text-warning" style="font-size: 0.8rem; white-space: nowrap;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= $r['rating'] ? '-fill' : '' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.82rem; max-width: 150px;" class="text-wrap">
                                        <span class="text-white-50"><?= htmlspecialchars($r['komentar'] ?: '-') ?></span>
                                    </div>
                                </td>
                                <td class="pe-4">
                                    <small class="text-muted" style="font-size: 0.72rem;"><?= date('d/m/y H:i', strtotime($r['created_at'])) ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($ratings)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">Belum ada rating ulasan diberikan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require VIEW_PATH . '/layouts/superadmin.php'; ?>
