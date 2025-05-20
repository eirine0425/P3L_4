<?php

namespace App\Repositories\Eloquent;

use App\Models\TransaksiPenitipan;
use App\Repositories\Interfaces\TransaksiPenitipanRepositoryInterface;

class TransaksiPenitipanRepository implements TransaksiPenitipanRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array
    {
        return TransaksiPenitipan::with(['penitip', 'barang'])
            ->where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('metode_penitipan', 'like', "%{$search}%")
                        ->orWhere('status_penitipan', 'like', "%{$search}%");
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?TransaksiPenitipan
    {
        return TransaksiPenitipan::find($id);
    }

    public function create(array $data): TransaksiPenitipan
    {
        return TransaksiPenitipan::create($data);
    }

    public function update(int $id, array $data): TransaksiPenitipan
    {
        $transaksiPenitipan = TransaksiPenitipan::findOrFail($id);
        $transaksiPenitipan->update($data);
        return $transaksiPenitipan;
    }

    public function delete(int $id): bool
    {
        return TransaksiPenitipan::destroy($id) > 0;
    }
}