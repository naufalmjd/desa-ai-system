<?php

declare(strict_types=1);

namespace Controller\Warga;

use Core\Controller;
use Service\AIService;

/**
 * ChatbotController — AI Chatbot Gemini untuk layanan publik.
 */
final class ChatbotController extends Controller
{
    // GET /warga/chatbot/index
    public function index(): void
    {
        $userId    = $this->authId();
        $sessionId = $this->session->get('chat_session_id') ?? bin2hex(random_bytes(16));
        $this->session->set('chat_session_id', $sessionId);

        // Ambil riwayat percakapan sesi ini
        $history = $this->db->fetchAll(
            'SELECT role, content, created_at FROM chat_history
             WHERE user_id=? AND session_id=? ORDER BY created_at ASC LIMIT 50',
            [$userId, $sessionId]
        );

        $csrfToken = $this->session->generateCsrfToken();
        $this->render('warga/chatbot/index', compact('history', 'csrfToken', 'sessionId'), 'warga');
    }

    // POST /warga/chatbot/send — AJAX
    public function send(): void
    {
        if (!$this->isAjax()) $this->abort(400);

        $userId    = $this->authId();
        $message   = $this->clean($this->input('message', ''));
        $sessionId = $this->clean($this->input('session_id', ''));

        if (!$message) {
            $this->jsonError('Pesan tidak boleh kosong.');
        }

        // Ambil 10 pesan terakhir sebagai konteks
        $history = $this->db->fetchAll(
            'SELECT role, content FROM chat_history
             WHERE user_id=? AND session_id=? ORDER BY created_at DESC LIMIT 10',
            [$userId, $sessionId]
        );
        $history = array_reverse($history);

        // Simpan pesan user
        $userMsgId = (int)$this->db->insert('chat_history', [
            'user_id'    => $userId,
            'session_id' => $sessionId,
            'role'       => 'user',
            'content'    => $message,
        ]);

        try {
            $ai     = new AIService();
            $result = $ai->chat($message, $history);

            // Simpan jawaban AI
            $aiMsgId = (int)$this->db->insert('chat_history', [
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'role'       => 'assistant',
                'content'    => $result['reply'],
                'tokens'     => $result['tokens'] ?? null,
            ]);

            $this->jsonSuccess([
                'reply'      => $result['reply'],
                'msg_id'     => $aiMsgId,
                'time'       => date('H:i'),
            ]);
        } catch (\Throwable $e) {
            $this->jsonError('AI server sedang tidak tersedia. Coba lagi nanti.');
        }
    }

    // POST /warga/chatbot/rate — Rating jawaban AI
    public function rate(): void
    {
        if (!$this->isAjax()) $this->abort(400);

        $chatId  = (int)$this->input('chat_id');
        $rating  = (int)$this->input('rating');

        if ($chatId < 1 || $rating < 1 || $rating > 5) {
            $this->jsonError('Data tidak valid.');
        }

        $this->db->insert('chat_rating', [
            'chat_id'  => $chatId,
            'user_id'  => $this->authId(),
            'rating'   => $rating,
            'komentar' => $this->clean($this->input('komentar', '')),
        ]);

        $this->jsonSuccess(null, 'Terima kasih atas penilaian Anda!');
    }

    // POST /warga/chatbot/clear — Bersihkan sesi chat
    public function clear(): void
    {
        $this->session->remove('chat_session_id');
        $this->redirect('warga/chatbot');
    }
}
