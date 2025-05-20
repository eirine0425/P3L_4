<?php

namespace App\Repositories\Eloquent;

use App\Models\Komisi;
use App\Repositories\Interfaces\KomisiRepositoryInterface;

class KomisiRepository implements KomisiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array
    {
        return Komisi::with(['pegawai', 'penitip', 'barang'])
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $pegawaiId, int $penitipId, int $barangId): ?Komisi
    {
        return Komisi::where('pegawai_id', $pegawaiId)
            ->where('penitip_id', $penitipId)
            ->where('barang_id', $barangId)
            ->first();
    }

    public function create(array $data): Komisi
    {
        return Komisi::create($data);
    }

    public function update(int $pegawaiId, int $penitipId, int $barangId, array $data): Komisi
    {
        $komisi = Komisi::where('pegawai_id', $pegawaiId)
            ->where('penitip_id', $penitipId)
            ->where('barang_id', $barangId)
            ->firstOrFail();
        $komisi->update($data);
        return $komisi;
    }

    public function delete(int $pegawaiId, int $penitipId, int $barangId): bool
    {
        return Komisi::where('pegawai_id', $pegawaiId)
            ->where('penitip_id', $penitipId)
            ->where('barang_id', $barangId)
            ->delete() > 0;
    }
}