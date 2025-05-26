<?php

namespace App\Repositories\Interfaces;

use App\Models\DetailTransaksi;

interface DetailTransaksiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array;
    public function find(int $barangId, int $transaksiId): ?DetailTransaksi;
    public function create(array $data): DetailTransaksi;
    public function update(int $barangId, int $transaksiId, array $data): DetailTransaksi;
    public function delete(int $barangId, int $transaksiId): bool;
}