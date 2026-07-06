<?php

declare(strict_types=1);

namespace Controller\Superadmin;

use Core\Controller;

/**
 * ChatbotController — Pantau Percakapan AI Chatbot & Feedback (Super Admin)
 */
final class ChatbotController extends Controller
{
    // GET /superadmin/chatbot
    public function index(): void
    {
        // 1. Grouped chat sessions
        $sessions = $this->db->fetchAll(
            "SELECT 
                h.session_id, 
                u.username,
                MIN(h.created_at) AS started_at,
                COUNT(h.id) AS message_count
             FROM chat_history h
             JOIN users u ON u.id = h.user_id
             GROUP BY h.session_id, u.username
             ORDER BY started_at DESC"
        );

        // 2. Chat ratings
        $ratings = $this->db->fetchAll(
            "SELECT r.*, u.username, h.content AS message_content
             FROM chat_rating r
             JOIN users u ON u.id = r.user_id
             JOIN chat_history h ON h.id = r.chat_id
             ORDER BY r.created_at DESC"
        );

        $flash = $this->getFlash();
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('superadmin/chatbot/index', compact('sessions', 'ratings', 'flash', 'csrfToken'), 'superadmin');
    }

    // GET /superadmin/chatbot/session/{sessionId}
    public function session(string $sessionId): void
    {
        $messages = $this->db->fetchAll(
            "SELECT h.*, u.username
             FROM chat_history h
             JOIN users u ON u.id = h.user_id
             WHERE h.session_id = ?
             ORDER BY h.created_at ASC",
            [$sessionId]
        );

        if (empty($messages)) {
            $this->abort(404, 'Sesi percakapan tidak ditemukan.');
        }

        $sessionUser = $messages[0]['username'];
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('superadmin/chatbot/session', compact('messages', 'sessionId', 'sessionUser', 'csrfToken'), 'superadmin');
    }
}
