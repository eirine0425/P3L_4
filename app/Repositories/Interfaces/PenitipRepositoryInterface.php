<?php
namespace App\Repositories\Interfaces;

use App\Models\Penitip; // Pastikan ini konsisten

interface PenitipRepositoryInterface
{
    public function getAll(): array;
    public function find(int $id): ?Penitip;
    public function create(array $data): Penitip;
    public function update(int $id, array $data): Penitip;
    public function delete(int $id): bool;
}