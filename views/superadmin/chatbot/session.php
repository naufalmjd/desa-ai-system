<?php $pageTitle = 'Detail Sesi Percakapan AI'; ?>

<?php ob_start(); ?>

<div class="row justify-content-center" data-aos="fade-up">
    <div class="col-lg-8">
        <div class="card border-0">
            <!-- Header -->
            <div class="card-header bg-transparent py-3 border-0 d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/superadmin/chatbot" class="btn btn-sm btn-outline-secondary p-1 border-0" style="border-radius: 8px;"><i class="bi bi-arrow-left fs-5 text-white"></i></a>
                <div>
                    <span class="fs-6 fw-bold text-white d-block">Percakapan: <?= htmlspecialchars($sessionUser) ?></span>
                    <small class="text-muted" style="font-size: 0.72rem;">Sesi ID: <code><?= htmlspecialchars($sessionId) ?></code></small>
                </div>
            </div>

            <!-- Dialogue History -->
            <div class="card-body p-4 bg-black bg-opacity-25" style="max-height: 550px; overflow-y: auto; border-top: 1px solid rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.03);">
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($messages as $msg): 
                        $isUser = $msg['role'] === 'user';
                    ?>
                    <div class="d-flex flex-column <?= $isUser ? 'align-items-end' : 'align-items-start' ?>">
                        <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.72rem;">
                            <span class="text-white-50"><?= $isUser ? htmlspecialchars($msg['username']) : 'Gemini AI Assistant' ?></span>
                        </div>
                        <div class="p-3 rounded-4 bubble-<?= $isUser ? 'user' : 'ai' ?>" style="max-width: 80%; line-height: 1.5; font-size: 0.88rem; <?= $isUser ? 'border-bottom-right-radius: 0 !important;' : 'border-bottom-left-radius: 0 !important;' ?>">
                            
                            <?= nl2br(htmlspecialchars($msg['content'])) ?>
                        </div>
                        <small class="text-muted mt-1" style="font-size: 0.65rem;"><?= date('H:i:s, d/m/Y', strtotime($msg['created_at'])) ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="card-footer bg-transparent py-3 text-center border-0">
                <span class="text-muted" style="font-size: 0.78rem;"><i class="bi bi-shield-fill-check text-success me-1"></i> Audit log diamankan oleh protokol keamanan SIAP-Desa</span>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require VIEW_PATH . '/layouts/superadmin.php'; ?>
