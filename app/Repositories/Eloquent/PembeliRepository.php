<?php

namespace App\Repositories\Eloquent;

use App\Models\Pembeli;
use App\Repositories\Interfaces\PembeliRepositoryInterface;

class PembeliRepository implements PembeliRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1, ): array
    {
        return Pembeli::where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('nama', 'like', '%' . $search . '%');
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?Pembeli
    {
        return Pembeli::where('pembeli_id', $id)->first(); // Menggunakan pembeli_id sebagai primary key
    }

    public function create(array $data): Pembeli
    {
        return Pembeli::create($data);
    }

    public function update(int $id, array $data): Pembeli
    {
        $pembeli = $this->find($id);
        if ($pembeli) {
            $pembeli->update($data); // Update data pembeli
        }
        return $pembeli;
    }

    public function delete(int $id): bool
    {
        $pembeli = $this->find($id);
        if ($pembeli) {
            return $pembeli->delete(); // Menghapus pembeli
        }
        return false;
    }
}