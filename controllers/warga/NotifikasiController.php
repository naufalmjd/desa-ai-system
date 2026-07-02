<?php

declare(strict_types=1);

namespace Controller\Warga;

use Core\Controller;

/**
 * NotifikasiController — Riwayat notifikasi sistem untuk warga.
 */
final class NotifikasiController extends Controller
{
    public function index(): void
    {
        $userId = $this->authId();
        
        // Mark all as read when opening notifications page
        $this->db->update('notifikasi', ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')], 'user_id = ? AND is_read = 0', [$userId]);

        $notifikasi = $this->db->fetchAll(
            'SELECT * FROM notifikasi WHERE user_id = ? ORDER BY created_at DESC LIMIT 50',
            [$userId]
        );
        $user = $this->auth();

        $this->render('warga/notifikasi/index', compact('notifikasi', 'user'), 'warga');
    }
}
