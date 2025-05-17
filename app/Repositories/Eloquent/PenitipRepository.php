<?php

namespace App\Repositories\Eloquent;

use App\Models\Penitip;
use App\Repositories\Interfaces\PenitipRepositoryInterface;

class PenitipRepository implements PenitipRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1, ): array
    {
        return Penitip::where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('nama', 'like', '%' . $search . '%');
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?Penitip
    {
        return Penitip::where('pembeli_id', $id)->first(); // Menggunakan pembeli_id sebagai primary key
    }

    public function create(array $data): Penitip
    {
        return Penitip::create($data);
    }

    public function update(int $id, array $data): Penitip
    {
        $penitip = $this->find($id);
        if ($penitip) {
            $penitip->update($data); // Update data pembeli
        }
        return $penitip;
    }

    public function delete(int $id): bool
    {
        $penitip = $this->find($id);
        if ($penitip) {
            return $penitip->delete(); // Menghapus pembeli
        }
        return false;
    }
}