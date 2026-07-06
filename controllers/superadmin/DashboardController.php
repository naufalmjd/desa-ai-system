<?php

declare(strict_types=1);

namespace Controller\Superadmin;

use Core\Controller;

/**
 * DashboardController — Super Admin Dashboard
 */
final class DashboardController extends Controller
{
    public function index(): void
    {
        $stats = [
            'total_users'      => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM users WHERE deleted_at IS NULL'),
            'total_warga'      => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM users WHERE role_id = 1 AND deleted_at IS NULL'),
            'total_admin'      => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM users WHERE role_id = 2 AND deleted_at IS NULL'),
            'total_kades'      => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM users WHERE role_id = 3 AND deleted_at IS NULL'),
            'total_superadmin' => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM users WHERE role_id = 4 AND deleted_at IS NULL'),
            'total_logs'       => (int)$this->db->fetchColumn('SELECT COUNT(*) FROM log_aktivitas'),
        ];

        // Recent activity logs
        $recentLogs = $this->db->fetchAll(
            'SELECT l.*, u.username 
             FROM log_aktivitas l 
             LEFT JOIN users u ON u.id = l.user_id 
             ORDER BY l.created_at DESC LIMIT 6'
        );

        $flash = $this->getFlash();

        $this->render('superadmin/dashboard/index', compact('stats', 'recentLogs', 'flash'), 'superadmin');
    }
}
