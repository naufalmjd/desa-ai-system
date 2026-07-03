<?php

declare(strict_types=1);

namespace Repository;

use Core\Database;
use Repository\Interfaces\UserRepositoryInterface;

/**
 * UserRepository — Implementasi Repository Pattern untuk tabel users.
 * Semua query menggunakan Prepared Statement (PDO) untuk mencegah SQL Injection.
 */
final class UserRepository implements UserRepositoryInterface
{
    public function __construct(private Database $db) {}

    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT u.*, r.name AS role_name, r.label AS role_label, r.permissions
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = ? AND u.deleted_at IS NULL AND u.is_active = 1',
            [$id]
        );
    }

    public function findByUsername(string $username): array|false
    {
        return $this->db->fetchOne(
            'SELECT u.*, r.name AS role_name, r.label AS role_label
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.username = ? AND u.deleted_at IS NULL',
            [$username]
        );
    }

    public function findByEmail(string $email): array|false
    {
        return $this->db->fetchOne(
            'SELECT u.*, r.name AS role_name, r.label AS role_label
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.email = ? AND u.deleted_at IS NULL',
            [$email]
        );
    }

    public function findByUsernameOrEmail(string $identifier): array|false
    {
        return $this->db->fetchOne(
            'SELECT u.*, r.name AS role_name, r.label AS role_label
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE (u.username = ? OR u.email = ?) AND u.deleted_at IS NULL',
            [$identifier, $identifier]
        );
    }

    public function create(array $data): int
    {
        return (int)$this->db->insert('users', $data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->update('users', $data, 'id = ?', [$id]) > 0;
    }

    public function updateLoginAttempts(int $id, int $attempts, ?string $lockedUntil = null): void
    {
        $data = ['login_attempts' => $attempts];
        if ($lockedUntil !== null) {
            $data['locked_until'] = $lockedUntil;
        }
        $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function updateLastLogin(int $id, string $ip): void
    {
        $this->db->update('users', [
            'last_login_at'  => date('Y-m-d H:i:s'),
            'last_login_ip'  => $ip,
            'login_attempts' => 0,
            'locked_until'   => null,
        ], 'id = ?', [$id]);
    }

    public function updateRememberToken(int $id, string $token): void
    {
        $this->db->update('users', ['remember_token' => $token], 'id = ?', [$id]);
    }

    public function findByRememberToken(string $token): array|false
    {
        return $this->db->fetchOne(
            'SELECT u.*, r.name AS role_name
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.remember_token = ? AND u.deleted_at IS NULL AND u.is_active = 1',
            [$token]
        );
    }

    public function findAll(array $filters = [], int $page = 1): array
    {
        $where    = ['u.deleted_at IS NULL'];
        $bindings = [];

        if (!empty($filters['role_id'])) {
            $where[]    = 'u.role_id = ?';
            $bindings[] = $filters['role_id'];
        }
        if (!empty($filters['search'])) {
            $where[]    = '(u.username LIKE ? OR u.email LIKE ?)';
            $bindings[] = '%' . $filters['search'] . '%';
            $bindings[] = '%' . $filters['search'] . '%';
        }

        $sql = 'SELECT u.id, u.username, u.email, u.is_active, u.last_login_at,
                       r.name AS role_name, r.label AS role_label
                FROM users u JOIN roles r ON r.id = u.role_id
                WHERE ' . implode(' AND ', $where) . ' ORDER BY u.created_at DESC';

        return $this->db->paginate($sql, $bindings, $page);
    }

    public function softDelete(int $id): bool
    {
        return $this->db->softDelete('users', 'id = ?', [$id]) > 0;
    }
}
