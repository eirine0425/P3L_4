<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search, int $page = 1): array;
    public function find(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): User;
    public function delete(int $id): bool;
    public function getUserbyEmail(string $email): ?User;
}
