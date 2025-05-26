<?php

namespace App\Repositories\Eloquent;

use App\Models\Alamat;
use App\Repositories\Interfaces\AlamatRepositoryInterface;

class AlamatRepository implements AlamatRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1, ): array
    {
        return Alamat::where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('nama', 'like', '%' . $search . '%');
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?Alamat
    {
        return Alamat::find($id);
    }

    public function create(array $data): Alamat
    {
        return Alamat::create($data);
    }

    public function update(int $id, array $data): Alamat
    {
        $alamat = Alamat::findOrFail($id);
        $alamat->update($data);
        return $alamat;
    }

    public function delete(int $id): bool
    {
        return Alamat::destroy($id) > 0;
    }
}