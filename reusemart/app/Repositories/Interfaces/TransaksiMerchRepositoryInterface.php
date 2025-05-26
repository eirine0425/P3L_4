<?php

namespace App\Repositories\Interfaces;

use App\Models\TransaksiMerch;

interface TransaksiMerchRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array;
    public function find(int $pembeliId, int $merchId, string $tanggalPenukaran): ?TransaksiMerch;
    public function create(array $data): TransaksiMerch;
    public function update(int $pembeliId, int $merchId, string $tanggalPenukaran, array $data): TransaksiMerch;
    public function delete(int $pembeliId, int $merchId, string $tanggalPenukaran): bool;
}