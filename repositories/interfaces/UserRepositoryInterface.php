<?php

declare(strict_types=1);

namespace Repository\Interfaces;

interface UserRepositoryInterface
{
    public function findById(int $id): array|false;
    public function findByUsername(string $username): array|false;
    public function findByEmail(string $email): array|false;
    public function findByUsernameOrEmail(string $identifier): array|false;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function updateLoginAttempts(int $id, int $attempts, ?string $lockedUntil = null): void;
    public function updateLastLogin(int $id, string $ip): void;
    public function updateRememberToken(int $id, string $token): void;
    public function findByRememberToken(string $token): array|false;
    public function findAll(array $filters = [], int $page = 1): array;
    public function softDelete(int $id): bool;
}
