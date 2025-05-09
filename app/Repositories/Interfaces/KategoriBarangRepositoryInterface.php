<?php

namespace App\Repositories\Interfaces;

use App\Models\KategoriBarang;

interface KategoriBarangRepositoryInterface
{
    public function getAll(): array;
    public function find(int $id): ?KategoriBarang;
    public function create(array $data): KategoriBarang;
    public function update(int $id, array $data): KategoriBarang;
    public function delete(int $id): bool;
}