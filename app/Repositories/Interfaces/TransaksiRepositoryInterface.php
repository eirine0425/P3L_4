<?php

namespace App\Repositories\Interfaces;

use App\Models\Transaksi;

interface TransaksiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array;
    public function find(int $id): ?Transaksi;
    public function create(array $data): Transaksi;
    public function update(int $id, array $data): Transaksi;
    public function delete(int $id): bool;
}