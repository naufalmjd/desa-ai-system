<?php

declare(strict_types=1);

namespace Controller\Superadmin;

use Core\Controller;

/**
 * LogController — Lihat Log Aktivitas Sistem (Super Admin)
 */
final class LogController extends Controller
{
    public function index(): void
    {
        $search = $this->clean($this->input('q', ''));
        $module = $this->clean($this->input('module', ''));
        $page   = max(1, (int)$this->input('page', 1));

        $where = [];
        $bindings = [];

        if ($search !== '') {
            $where[] = '(l.description LIKE ? OR l.ip_address LIKE ? OR u.username LIKE ?)';
            $bindings[] = '%' . $search . '%';
            $bindings[] = '%' . $search . '%';
            $bindings[] = '%' . $search . '%';
        }

        if ($module !== '') {
            $where[] = 'l.module = ?';
            $bindings[] = $module;
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT l.*, u.username 
                FROM log_aktivitas l 
                LEFT JOIN users u ON u.id = l.user_id 
                $whereSql 
                ORDER BY l.created_at DESC";

        $result = $this->db->paginate($sql, $bindings, $page, 20); // 20 logs per page

        // Fetch distinct modules for filter
        $modules = $this->db->fetchAll('SELECT DISTINCT module FROM log_aktivitas WHERE module IS NOT NULL AND module != ""');

        $this->render('superadmin/log/index', compact('result', 'search', 'module', 'modules'), 'superadmin');
    }
}
