<?php

namespace App\Repositories\Eloquent;

use App\Models\Barang;
use App\Repositories\Interfaces\BarangRepositoryInterface;

class BarangRepository implements BarangRepositoryInterface
{
    public function getAll(): array
    {
        return Barang::all()->toArray();
    }

    public function find(int $id): ?Barang
    {
        return Barang::find($id);
    }

    public function create(array $data): Barang
    {
        return Barang::create($data);
    }

    public function update(int $id, array $data): Barang
    {
        $barang = Barang::findOrFail($id);
        $barang->update($data);
        return $barang;
    }

    public function delete(int $id): bool
    {
        return Barang::destroy($id) > 0;
    }
}