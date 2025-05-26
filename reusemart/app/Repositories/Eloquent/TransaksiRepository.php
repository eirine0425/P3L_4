<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaksi;
use App\Repositories\Interfaces\TransaksiRepositoryInterface;

class TransaksiRepository implements TransaksiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array
    {
        return Transaksi::with(['pembeli', 'customerService'])
            ->where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('status_transaksi', 'like', "%{$search}%")
                        ->orWhere('metode_pengiriman', 'like', "%{$search}%");
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?Transaksi
    {
        return Transaksi::find($id);
    }

    public function create(array $data): Transaksi
    {
        return Transaksi::create($data);
    }

    public function update(int $id, array $data): Transaksi
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update($data);
        return $transaksi;
    }

    public function delete(int $id): bool
    {
        return Transaksi::destroy($id) > 0;
    }
}