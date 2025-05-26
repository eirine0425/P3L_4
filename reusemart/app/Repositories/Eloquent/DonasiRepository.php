<?php

namespace App\Repositories\Eloquent;

use App\Models\Donasi;
use App\Repositories\Interfaces\DonasiRepositoryInterface;

class DonasiRepository implements DonasiRepositoryInterface
{
    // Mengambil semua data donasi
    public function getAll(int $perPage = 10, string $search = "", int $page = 1, ): array
    {
        return Donasi::where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('barang_id', 'like', '%' . $search . '%');
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    // Menemukan donasi berdasarkan ID
    public function find(int $id): ?Donasi
    {
        return Donasi::find($id);
    }

    // Membuat data donasi baru
    public function create(array $data): Donasi
    {
        return Donasi::create($data);
    }

    // Mengupdate data donasi berdasarkan ID
    public function update(int $id, array $data): Donasi
    {
        $donasi = Donasi::findOrFail($id);
        $donasi->update($data);
        return $donasi;
    }

    // Menghapus data donasi berdasarkan ID
    public function delete(int $id): bool
    {
        return Donasi::destroy($id) > 0;
    }
}