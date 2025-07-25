<?php

namespace App\Repositories\Interfaces;

use App\Models\Barang;

interface BarangRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search ="", int $page = 1): array;
    public function find(int $id): ?Barang;
    public function create(array $data): Barang;
    public function update(int $id, array $data): Barang;
    public function delete(int $id): bool;
    public function searchByPenitip(int $penitipId, array $filters = [], int $perPage = 10, int $page = 1): array;
    public function getAdvancedSearch(array $filters = [], int $perPage = 10, int $page = 1): array;
    public function getStatusCounts(int $penitipId = null): array;
}