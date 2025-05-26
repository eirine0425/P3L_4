<?php

namespace App\Repositories\Interfaces;

use App\Models\Komisi;

interface KomisiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array;
    public function find(int $pegawaiId, int $penitipId, int $barangId): ?Komisi;
    public function create(array $data): Komisi;
    public function update(int $pegawaiId, int $penitipId, int $barangId, array $data): Komisi;
    public function delete(int $pegawaiId, int $penitipId, int $barangId): bool;
}