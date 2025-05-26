<?php

namespace App\Repositories\Interfaces;

use App\Models\Garansi;

interface GaransiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search ="", int $page = 1): array;
    public function find(int $id): ?Garansi;
    public function create(array $data): Garansi;
    public function update(int $id, array $data): ?Garansi;
    public function delete(int $id): bool;
}
