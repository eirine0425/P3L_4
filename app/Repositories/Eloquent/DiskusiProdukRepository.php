<?php

namespace App\Repositories\Eloquent;

use App\Models\DiskusiProduk;
use App\Repositories\Interfaces\DiskusiProdukRepositoryInterface;

class DiskusiProdukRepository implements DiskusiProdukRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array
    {
        return DiskusiProduk::with(['pembeli', 'barang'])
            ->where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('pertanyaan', 'like', "%{$search}%")
                        ->orWhere('jawaban', 'like', "%{$search}%");
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?DiskusiProduk
    {
        return DiskusiProduk::find($id);
    }

    public function create(array $data): DiskusiProduk
    {
        return DiskusiProduk::create($data);
    }

    public function update(int $id, array $data): DiskusiProduk
    {
        $diskusiProduk = DiskusiProduk::findOrFail($id);
        $diskusiProduk->update($data);
        return $diskusiProduk;
    }

    public function delete(int $id): bool
    {
        return DiskusiProduk::destroy($id) > 0;
    }
}