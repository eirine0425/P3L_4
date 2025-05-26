<?php

namespace App\Repositories\Interfaces;

use App\Models\Pembeli;

interface PembeliRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search ="", int $page = 1): array;
    public function find(int $id): ?Pembeli;
    public function create(array $data): Pembeli;
    public function update(int $id, array $data): Pembeli;
    public function delete(int $id): bool;
}