<?php

namespace App\Repositories\Interfaces;

use App\Models\TransaksiPenitipan;

interface TransaksiPenitipanRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array;
    public function find(int $id): ?TransaksiPenitipan;
    public function create(array $data): TransaksiPenitipan;
    public function update(int $id, array $data): TransaksiPenitipan;
    public function delete(int $id): bool;
    public function getByPenitipId(int $penitipId): array;
}