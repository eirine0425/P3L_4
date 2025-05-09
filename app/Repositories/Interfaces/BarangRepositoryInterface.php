<?php

namespace App\Repositories\Interfaces;

use App\Models\Barang;

interface BarangRepositoryInterface
{
    public function getAll(): array;
    public function find(int $id): ?Barang;
    public function create(array $data): Barang;
    public function update(int $id, array $data): Barang;
    public function delete(int $id): bool;
}