<?php

declare(strict_types=1);

namespace Controller\Superadmin;

use Core\Controller;

/**
 * BackupController — Kelola Backup & Restore Database (Super Admin)
 */
final class BackupController extends Controller
{
    private string $backupDir;

    public function __construct()
    {
        parent::__construct();
        $this->backupDir = ROOT_PATH . '/database/backups';
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    // GET /superadmin/backup
    public function index(): void
    {
        $dbCfg = require CONFIG_PATH . '/database.php';
        $dbName = $dbCfg['dbname'] ?? 'desa_ai_system';

        // Fetch DB Stats
        $tableCount = (int)$this->db->fetchColumn(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ?',
            [$dbName]
        );

        $dbSizeBytes = (float)$this->db->fetchColumn(
            'SELECT SUM(data_length + index_length) FROM information_schema.tables WHERE table_schema = ?',
            [$dbName]
        );

        // Scan backup directory
        $files = [];
        if (is_dir($this->backupDir)) {
            $scan = scandir($this->backupDir);
            foreach ($scan as $file) {
                if (str_ends_with($file, '.sql')) {
                    $filePath = $this->backupDir . '/' . $file;
                    $files[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'created_at' => filemtime($filePath),
                    ];
                }
            }
        }

        // Sort backups by time descending
        usort($files, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        $flash = $this->getFlash();
        $csrfToken = $this->session->generateCsrfToken();

        $this->render('superadmin/backup/index', compact('tableCount', 'dbSizeBytes', 'dbName', 'files', 'flash', 'csrfToken'), 'superadmin');
    }

    // POST /superadmin/backup/create
    public function create(): void
    {
        try {
            $sql = $this->exportDatabase();
            $filename = 'backup-desa-' . date('Y-m-d-H-i-s') . '.sql';
            $filePath = $this->backupDir . '/' . $filename;

            file_put_contents($filePath, $sql);

            $this->logActivity('Membuat backup database: ' . $filename);
            $this->flash('success', 'Backup database berhasil dibuat: ' . $filename);
        } catch (\Throwable $e) {
            $this->flash('danger', 'Gagal membuat backup: ' . $e->getMessage());
        }

        $this->redirect('superadmin/backup');
    }

    // GET /superadmin/backup/download
    public function download(): void
    {
        $filename = basename($this->input('file', ''));
        $filePath = $this->backupDir . '/' . $filename;

        if ($filename && is_file($filePath)) {
            $this->logActivity('Mengunduh backup database: ' . $filename);
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            
            readfile($filePath);
            exit;
        }

        $this->flash('danger', 'File backup tidak ditemukan.');
        $this->redirect('superadmin/backup');
    }

    // POST /superadmin/backup/delete
    public function delete(): void
    {
        $filename = basename($this->input('file', ''));
        $filePath = $this->backupDir . '/' . $filename;

        if ($filename && is_file($filePath)) {
            unlink($filePath);
            $this->logActivity('Menghapus backup database: ' . $filename);
            
            if ($this->isAjax()) {
                $this->jsonSuccess(null, 'File backup berhasil dihapus.');
            }
            $this->flash('success', 'File backup berhasil dihapus.');
        } else {
            if ($this->isAjax()) {
                $this->jsonError('File backup tidak ditemukan.');
            }
            $this->flash('danger', 'File backup tidak ditemukan.');
        }

        $this->redirect('superadmin/backup');
    }

    private function exportDatabase(): string
    {
        $pdo = $this->db->getPdo();
        $tables = [];
        $result = $pdo->query('SHOW TABLES');
        while ($row = $result->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql = "-- SIAP-Desa Database Backup\n";
        $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- ========================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            // Get create table statement
            $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $row = $stmt->fetch(\PDO::FETCH_NUM);
            $sql .= "\n\nDROP TABLE IF EXISTS `$table`;\n";
            $sql .= $row[1] . ";\n\n";

            // Get rows data
            $stmt = $pdo->query("SELECT * FROM `$table`");
            $numCols = $stmt->columnCount();

            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $sql .= "INSERT INTO `$table` VALUES(";
                for ($j = 0; $j < $numCols; $j++) {
                    if (isset($row[$j])) {
                        $sql .= $pdo->quote((string)$row[$j]);
                    } else {
                        $sql .= 'NULL';
                    }
                    if ($j < ($numCols - 1)) {
                        $sql .= ',';
                    }
                }
                $sql .= ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";
        return $sql;
    }

    private function logActivity(string $desc): void
    {
        $this->db->insert('log_aktivitas', [
            'user_id'     => $this->authId(),
            'action'      => 'backup_database',
            'module'      => 'superadmin',
            'description' => $desc,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
