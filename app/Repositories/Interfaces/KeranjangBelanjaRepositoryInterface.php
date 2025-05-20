<?php

namespace App\Repositories\Interfaces;

use App\Models\KeranjangBelanja;

interface KeranjangBelanjaRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array;
    public function find(int $id): ?KeranjangBelanja;
    public function create(array $data): KeranjangBelanja;
    public function update(int $id, array $data): KeranjangBelanja;
    public function delete(int $id): bool;
}