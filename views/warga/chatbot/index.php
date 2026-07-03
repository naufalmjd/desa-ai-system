<?php $pageTitle = 'AI Chatbot'; ?>
<?php 
$user = $_SESSION['user'] ?? null;
$userInitials = strtoupper(substr($user['nama'] ?? 'U', 0, 2));
ob_start(); 
?>

<!-- Custom CSS for Premium Chatbot Styling -->
<style>
    /* Custom Scrollbar for Chat Messages */
    #chatMessages::-webkit-scrollbar {
        width: 6px;
    }
    #chatMessages::-webkit-scrollbar-track {
        background: transparent;
    }
    #chatMessages::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.08);
        border-radius: 10px;
    }
    #chatMessages::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.15);
    }

    /* Custom Focus Outline for Input */
    #chatInput:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12) !important;
        background: #ffffff !important;
    }

    /* FAQ Buttons Premium Micro-Animations */
    .faq-btn {
        background: rgba(255, 255, 255, 0.55);
        border: 1px solid rgba(0, 0, 0, 0.04) !important;
        color: #475569 !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .faq-btn:hover {
        background: #ffffff !important;
        border-color: rgba(59, 130, 246, 0.25) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.06);
        color: #1e3a8a !important;
    }
    .faq-btn:active {
        transform: translateY(0);
    }

    /* Floating Background Blobs Animation */
    @keyframes floatBlob1 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(30px, -20px) scale(1.05); }
    }
    @keyframes floatBlob2 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-20px, 30px) scale(1.08); }
    }
</style>

<div class="position-relative overflow-hidden w-100" style="height:calc(100vh - 160px); border-radius: 20px;">
    <!-- Glowing background blobs behind glassmorphic cards -->
    <div class="position-absolute rounded-circle bg-primary bg-opacity-10" style="width:320px; height:320px; top:-80px; left:-80px; filter: blur(90px); z-index:0; animation: floatBlob1 18s infinite ease-in-out;"></div>
    <div class="position-absolute rounded-circle bg-success bg-opacity-10" style="width:280px; height:280px; bottom:-60px; right:80px; filter: blur(90px); z-index:0; animation: floatBlob2 14s infinite ease-in-out;"></div>

    <div class="row g-3 h-100 position-relative z-1">
        <!-- Sidebar Chatbot (Glassmorphic) -->
        <div class="col-lg-3 d-none d-lg-flex flex-column gap-3 h-100">
            <!-- Profile Info Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.4) !important;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-gradient rounded-3 d-flex p-2.5 align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #10b981, #059669); width: 44px; height: 44px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-robot fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:.9rem; letter-spacing: -0.01em;">SIAP-Bot</div>
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="bg-success rounded-circle" style="width:6px;height:6px;display:inline-block; box-shadow: 0 0 8px #10b981;"></span>
                                <small class="text-success fw-semibold" style="font-size:.65rem">Online &middot; Gemini Pro</small>
                            </div>
                        </div>
                    </div>
                    <p class="text-secondary mb-0" style="font-size:.78rem; line-height: 1.55;">
                        Asisten AI layanan publik pintar <?= DESA_NAMA ?>. Siap menjawab pertanyaan Anda secara instan 24/7.
                    </p>
                </div>
            </div>

            <!-- Quick FAQ List -->
            <div class="card border-0 shadow-sm flex-grow-1 overflow-hidden d-flex flex-column" style="border-radius: 16px; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.4) !important;">
                <div class="card-header bg-transparent border-0 py-3 px-3.5 fw-bold text-dark d-flex align-items-center gap-2" style="font-size:.82rem;">
                    <span class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle text-warning" style="width: 24px; height: 24px;">
                        <i class="bi bi-lightning-charge-fill" style="font-size: .85rem;"></i>
                    </span>
                    Topik Cepat
                </div>
                <div class="card-body p-2 overflow-auto flex-grow-1">
                    <?php
                    $faqs = [
                        ['bi-file-earmark-text', 'Syarat membuat surat domisili'],
                        ['bi-credit-card-2-front', 'Cara pengajuan surat tidak mampu'],
                        ['bi-calendar2-heart', 'Jadwal posyandu balita'],
                        ['bi-gift', 'Informasi bantuan PKH'],
                        ['bi-exclamation-triangle', 'Cara melaporkan jalan rusak'],
                        ['bi-cash-coin', 'Info BLT Dana Desa'],
                        ['bi-signpost-split', 'Prosedur surat pindah domisili'],
                        ['bi-clock', 'Jam layanan kantor desa'],
                    ];
                    foreach ($faqs as [$icon, $faq]):
                    ?>
                    <button class="btn btn-sm text-start w-100 mb-1.5 faq-btn d-flex align-items-center gap-2"
                            style="font-size:.75rem; border-radius:10px; padding: 10px 12px; transition: all 0.25s ease;"
                            onclick="sendFaq('<?= addslashes($faq) ?>')">
                        <i class="bi <?= $icon ?> text-muted" style="font-size: .85rem;"></i>
                        <span class="text-truncate"><?= htmlspecialchars($faq) ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Clear Chat Sessi -->
            <div class="card border-0 shadow-sm flex-shrink-0" style="border-radius: 16px; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.4) !important;">
                <div class="card-body p-3">
                    <form method="POST" action="<?= APP_URL ?>/warga/chatbot/clear" class="d-grid">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <button type="submit" class="btn btn-outline-secondary btn-sm" style="border-radius: 10px; font-size: .78rem; padding: 8px 12px;">
                            <i class="bi bi-trash me-1"></i>Bersihkan Percakapan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Chat Area (Glassmorphic) -->
        <div class="col-lg-9 d-flex flex-column h-100">
            <div class="card border-0 shadow-sm flex-grow-1 d-flex flex-column overflow-hidden" style="border-radius: 16px; background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.4) !important;">
                <!-- Chat Header -->
                <div class="card-header bg-transparent border-0 d-flex align-items-center gap-3 py-3 px-3.5 border-bottom" style="border-color: rgba(0,0,0,0.04) !important;">
                    <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width:38px;height:38px;background:linear-gradient(135deg,#059669,#1e4080); box-shadow: 0 4px 10px rgba(5,150,105,0.15)">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size:.88rem; letter-spacing: -0.01em;">AI Asisten <?= DESA_NAMA ?></div>
                        <small class="text-success fw-semibold" style="font-size:.68rem">
                            <i class="bi bi-circle-fill me-1" style="font-size:.4rem"></i>
                            Didukung Google Gemini Pro
                        </small>
                    </div>
                    <div class="ms-auto">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5" style="border-radius: 6px; font-size: .7rem;">Gemini Pro</span>
                    </div>
                </div>

                <!-- Messages container -->
                <div class="card-body flex-grow-1 overflow-y-auto p-3.5 d-flex flex-column gap-3" id="chatMessages" style="background: rgba(248, 250, 252, 0.45);">
                    <!-- Welcome Msg -->
                    <div class="d-flex gap-3 mb-2" id="welcomeMsg">
                        <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                             style="width:36px; height:36px; background:linear-gradient(135deg,#059669,#1e4080); box-shadow: 0 4px 10px rgba(5,150,105,0.15); margin-top:2px;">
                            <i class="bi bi-robot" style="font-size:.85rem"></i>
                        </div>
                        <div>
                            <div class="rounded-4 p-4 shadow-sm" style="max-width:560px; font-size:.85rem; background: #ffffff; border: 1px solid rgba(0,0,0,0.04); border-top-left-radius: 4px; line-height: 1.6; color: #334155;">
                                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: .95rem;">
                                    Halo! Saya <strong>SIAP-Bot</strong> 👋
                                </h5>
                                <p class="mb-3 text-secondary">
                                    Asisten virtual pintar <?= DESA_NAMA ?>. Saya siap membantu menjawab pertanyaan Anda seputar layanan administrasi, pengaduan, dan informasi desa.
                                </p>
                                <div class="p-3 bg-light rounded-3 border-0 mb-3" style="font-size: .8rem;">
                                    <div class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1.5 text-primary"></i> Anda dapat menanyakan tentang:</div>
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1.5 text-secondary">
                                        <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Persyaratan & alur pengajuan surat</li>
                                        <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Pelaporan pengaduan infrastruktur & sosial</li>
                                        <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Program bantuan sosial (Bansos, PKH, BLT)</li>
                                        <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Jadwal pelayanan & agenda kegiatan desa</li>
                                    </ul>
                                </div>
                                <span class="text-primary fw-semibold">Apa yang bisa saya bantu untuk Anda hari ini?</span>
                            </div>
                            <small class="text-muted ms-2" style="font-size:.65rem">SIAP-Bot &middot; Baru saja</small>
                        </div>
                    </div>

                    <!-- Chat History -->
                    <?php foreach ($history as $msg): ?>
                    <?php if ($msg['role'] === 'user'): ?>
                    <div class="d-flex flex-row-reverse gap-3 mb-2">
                        <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                             style="width:36px; height:36px; background: linear-gradient(135deg, #1e4080, #3b82f6); box-shadow: 0 4px 10px rgba(30,64,128,0.15); margin-top:2px; font-size:.8rem; font-weight:700">
                            <?= $userInitials ?>
                        </div>
                        <div>
                            <div class="rounded-4 p-3 text-white shadow-sm" style="max-width:540px; font-size:.85rem; background: linear-gradient(135deg, #1e4080, #3b82f6); border-top-right-radius: 4px; line-height: 1.55;">
                                <?= htmlspecialchars($msg['content']) ?>
                            </div>
                            <div class="text-end mt-1">
                                <small class="text-muted" style="font-size:.65rem">
                                    <?= date('H:i', strtotime($msg['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="d-flex gap-3 mb-2">
                        <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                             style="width:36px; height:36px; background:linear-gradient(135deg,#059669,#1e4080); box-shadow: 0 4px 10px rgba(5,150,105,0.15); margin-top:2px;">
                            <i class="bi bi-robot" style="font-size:.85rem"></i>
                        </div>
                        <div>
                            <div class="rounded-4 p-3 bg-white shadow-sm" style="max-width:540px; font-size:.85rem; border: 1px solid rgba(0,0,0,0.04); border-top-left-radius: 4px; line-height: 1.55; color: #334155;">
                                <?= nl2br(htmlspecialchars($msg['content'])) ?>
                            </div>
                            <small class="text-muted ms-2 mt-1 d-block" style="font-size:.65rem">
                                SIAP-Bot - <?= date('H:i', strtotime($msg['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>

                    <div id="chatEnd"></div>
                </div>

                <!-- Input area -->
                <div class="card-footer bg-transparent border-0 p-3.5 border-top" style="border-color: rgba(0,0,0,0.04) !important;">
                    <div class="d-flex gap-2 position-relative">
                        <input type="text" id="chatInput" class="form-control"
                               placeholder="Ketik pertanyaan Anda di sini..."
                               style="border-radius:14px; font-size:.88rem; padding: 14px 60px 14px 16px; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 12px rgba(0,0,0,0.02); transition: all 0.3s ease; background: rgba(255,255,255,0.95);"
                               onkeydown="if(event.key==='Enter')sendMessage()">
                        <button class="btn btn-success d-flex align-items-center justify-content-center position-absolute" 
                                style="border-radius:10px; width: 44px; height: 44px; right: 6px; top: 6px; background: linear-gradient(135deg, #10b981, #059669); border: none; box-shadow: 0 4px 10px rgba(16,185,129,0.25); transition: all 0.2s ease;" 
                                onclick="sendMessage()" id="sendBtn">
                            <i class="bi bi-send-fill text-white fs-6"></i>
                        </button>
                    </div>
                    <div class="d-flex flex-wrap gap-1.5 mt-2.5 d-lg-none">
                        <?php foreach (array_slice($faqs, 0, 3) as [$icon, $faq]): ?>
                        <button class="btn btn-sm btn-light d-flex align-items-center gap-1.5" style="font-size:.68rem; border-radius:20px; padding: 6px 12px; background: rgba(255,255,255,0.8); border: 1px solid rgba(0,0,0,0.05); color: #475569;"
                                onclick="sendFaq('<?= addslashes($faq) ?>')">
                            <i class="bi <?= $icon ?> text-muted"></i>
                            <span><?= htmlspecialchars($faq) ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$escapedSessionId = htmlspecialchars($sessionId);
$footerExtra = <<<JS
<script>
const SESSION_ID = '{$escapedSessionId}';

function scrollToBottom(smooth = true) {
    const wrap = document.getElementById('chatMessages');
    if (wrap) {
        wrap.scrollTo({
            top: wrap.scrollHeight,
            behavior: smooth ? 'smooth' : 'auto'
        });
    }
}
scrollToBottom(false);

function sendFaq(text) {
    document.getElementById('chatInput').value = text;
    sendMessage();
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    const text  = input.value.trim();
    if (!text) return;

    // Tampilkan pesan user
    appendMessage('user', text);
    input.value = '';

    // Tampilkan loading
    appendTyping();

    const btn = document.getElementById('sendBtn');
    btn.disabled = true;

    fetch(APP_URL + '/warga/chatbot/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({
            _csrf_token: CSRF_TOKEN,
            message: text,
            session_id: SESSION_ID,
        })
    })
    .then(r => r.json())
    .then(res => {
        removeTyping();
        btn.disabled = false;
        if (res.success && res.data?.reply) {
            appendMessage('assistant', res.data.reply, res.data.time);
        } else {
            appendMessage('assistant', 'Maaf, terjadi kesalahan. Silakan coba lagi.');
        }
    })
    .catch(() => {
        removeTyping();
        btn.disabled = false;
        appendMessage('assistant', 'AI server sedang tidak tersedia. Silakan coba beberapa saat lagi.');
    });
}

function appendMessage(role, content, time) {
    const now = time || new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
    const wrap = document.getElementById('chatMessages');
    const isUser = role === 'user';
    const div = document.createElement('div');
    div.className = 'd-flex gap-3 mb-2' + (isUser ? ' flex-row-reverse' : '');
    div.innerHTML = isUser
        ? `<div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:36px; height:36px; background: linear-gradient(135deg, #1e4080, #3b82f6); box-shadow: 0 4px 10px rgba(30,64,128,0.15); margin-top:2px; font-size:.8rem; font-weight:700">{$userInitials}</div>
           <div><div class="rounded-4 p-3 text-white shadow-sm" style="max-width:540px; font-size:.85rem; background: linear-gradient(135deg, #1e4080, #3b82f6); border-top-right-radius: 4px; line-height: 1.55;">\${escHtml(content)}</div>
           <div class="text-end mt-1"><small class="text-muted" style="font-size:.65rem">\${now}</small></div></div>`
        : `<div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:36px; height:36px; background:linear-gradient(135deg,#059669,#1e4080); box-shadow: 0 4px 10px rgba(5,150,105,0.15); margin-top:2px;"><i class="bi bi-robot" style="font-size:.85rem"></i></div>
           <div><div class="rounded-4 p-3 bg-white shadow-sm" style="max-width:540px; font-size:.85rem; border: 1px solid rgba(0,0,0,0.04); border-top-left-radius: 4px; line-height: 1.55; color: #334155;">\${nl2brEsc(content)}</div>
           <small class="text-muted ms-2 mt-1 d-block" style="font-size:.65rem">SIAP-Bot - \${now}</small></div>`;
    wrap.insertBefore(div, document.getElementById('chatEnd'));
    scrollToBottom();
}

function appendTyping() {
    const div = document.createElement('div');
    div.id = 'typingIndicator';
    div.className = 'd-flex gap-3 mb-2';
    div.innerHTML = `<div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:36px; height:36px; background:linear-gradient(135deg,#059669,#1e4080); box-shadow: 0 4px 10px rgba(5,150,105,0.15); margin-top:2px;"><i class="bi bi-robot" style="font-size:.85rem"></i></div>
    <div class="rounded-4 p-3 bg-white shadow-sm d-flex gap-1.5 align-items-center" style="border: 1px solid rgba(0,0,0,0.04); border-top-left-radius: 4px;">
        \${[0,150,300].map(d=>`<div class="bg-secondary rounded-circle" style="width:6px;height:6px;animation:bounce .8s \${d}ms infinite"></div>`).join('')}
    </div>`;
    document.getElementById('chatMessages').insertBefore(div, document.getElementById('chatEnd'));
    scrollToBottom();
}

function removeTyping() {
    document.getElementById('typingIndicator')?.remove();
}

function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function nl2brEsc(s) {
    let html = escHtml(s);
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/`([^`]+)`/g, '<code class="bg-light px-1.5 py-0.5 rounded text-danger border font-monospace" style="font-size: 0.8rem;">$1</code>');
    html = html.replace(/^[\\*\\-]\\s+(.*?)$/gm, '<li class="ms-3 mb-1">$1</li>');
    html = html.replace(/(<li class="ms-3 mb-1">.*?<\\/li>)+/g, '<ul class="ps-3 mb-2">$&</ul>');
    html = html.replace(/^### (.*?)$/gm, '<h6 class="fw-bold my-2 text-dark" style="font-size:0.92rem">$1</h6>');
    html = html.replace(/^## (.*?)$/gm, '<h5 class="fw-bold my-2.5 text-dark" style="font-size:1.02rem">$1</h5>');
    html = html.replace(/\\n/g, '<br>');
    return html;
}
</script>
JS;
?>
<?php require VIEW_PATH . '/layouts/warga.php'; ?>
