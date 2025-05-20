<?php

namespace App\Repositories\Interfaces;

use App\Models\DiskusiProduk;

interface DiskusiProdukRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array;
    public function find(int $id): ?DiskusiProduk;
    public function create(array $data): DiskusiProduk;
    public function update(int $id, array $data): DiskusiProduk;
    public function delete(int $id): bool;
}