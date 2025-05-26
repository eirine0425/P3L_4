<?php

namespace App\Repositories\Eloquent;

use App\Models\KategoriBarang;
use App\Repositories\Interfaces\KategoriBarangRepositoryInterface;

class KategoriBarangRepository implements KategoriBarangRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "",int $page = 1): array
    {
        return KategoriBarang::
            where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('nama_kategori', 'like', "%{$search}%");
                        
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?KategoriBarang
    {
        return KategoriBarang::find($id);
    }

   public function create(array $data): KategoriBarang
    {
        return KategoriBarang::create($data);
    }

    public function update(int $id, array $data): KategoriBarang
    {
        $kategoriBarang = KategoriBarang::findOrFail($id);
        $kategoriBarang->update($data);
        return $kategoriBarang;
    }

    public function delete(int $id): bool
    {
        $kategoriBarang = $this->find($id);
        if ($kategoriBarang) {
            return $kategoriBarang->delete(); // Menghapus pembeli
        }
        return false;
    }
}