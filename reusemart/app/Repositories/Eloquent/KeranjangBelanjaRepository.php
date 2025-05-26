<?php

namespace App\Repositories\Eloquent;

use App\Models\KeranjangBelanja;
use App\Repositories\Interfaces\KeranjangBelanjaRepositoryInterface;

class KeranjangBelanjaRepository implements KeranjangBelanjaRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array
    {
        return KeranjangBelanja::with(['barang', 'pembeli'])
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?KeranjangBelanja
    {
        return KeranjangBelanja::find($id);
    }

    public function create(array $data): KeranjangBelanja
    {
        return KeranjangBelanja::create($data);
    }

    public function update(int $id, array $data): KeranjangBelanja
    {
        $keranjangBelanja = KeranjangBelanja::findOrFail($id);
        $keranjangBelanja->update($data);
        return $keranjangBelanja;
    }

    public function delete(int $id): bool
    {
        return KeranjangBelanja::destroy($id) > 0;
    }
}