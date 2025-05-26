<?php

namespace App\Repositories\Interfaces;

use App\Models\Pengiriman;

interface PengirimanRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array;
    public function find(int $id): ?Pengiriman;
    public function create(array $data): Pengiriman;
    public function update(int $id, array $data): Pengiriman;
    public function delete(int $id): bool;
}