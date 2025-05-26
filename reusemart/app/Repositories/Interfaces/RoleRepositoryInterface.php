<?php
namespace App\Repositories\Interfaces;

use App\Models\Role; // Pastikan ini konsisten

interface RoleRepositoryInterface
{
    public function getAll(): array;
    public function find(int $id): ?Role;
    public function create(array $data): Role;
    public function update(int $id, array $data): Role;
    public function delete(int $id): bool;
}