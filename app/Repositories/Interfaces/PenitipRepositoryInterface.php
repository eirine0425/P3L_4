<?php
namespace App\Repositories\Interfaces;

use App\Models\Penitip;
use App\DTOs\Penitip\GetPenitipPaginationRequest;

interface PenitipRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search ="", int $page = 1): array;
    public function find(int $id): ?Penitip;
    public function create(array $data): Penitip;
    public function update(int $id, array $data): Penitip;
    public function delete(int $id): bool;
}