<?php

namespace App\Repositories\Eloquent;

use App\Models\Pengiriman;
use App\Repositories\Interfaces\PengirimanRepositoryInterface;

class PengirimanRepository implements PengirimanRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array
    {
        return Pengiriman::with(['pengirim', 'transaksi', 'alamat'])
            ->where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('status_pengiriman', 'like', "%{$search}%")
                        ->orWhere('nama_penerima', 'like', "%{$search}%");
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?Pengiriman
    {
        return Pengiriman::find($id);
    }

    public function create(array $data): Pengiriman
    {
        return Pengiriman::create($data);
    }

    public function update(int $id, array $data): Pengiriman
    {
        $pengiriman = Pengiriman::findOrFail($id);
        $pengiriman->update($data);
        return $pengiriman;
    }

    public function delete(int $id): bool
    {
        return Pengiriman::destroy($id) > 0;
    }
}