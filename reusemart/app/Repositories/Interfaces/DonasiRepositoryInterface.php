<?php

namespace App\Repositories\Interfaces;

use App\Models\Donasi;

interface DonasiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search ="", int $page = 1): array;
    public function find(int $id): ?Donasi;
    public function create(array $data): Donasi;
    public function update(int $id, array $data): Donasi;
    public function delete(int $id): bool;
}