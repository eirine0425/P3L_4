<?php

namespace App\Repositories\Eloquent;

use App\Models\TransaksiMerch;
use App\Repositories\Interfaces\TransaksiMerchRepositoryInterface;

class TransaksiMerchRepository implements TransaksiMerchRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array
    {
        return TransaksiMerch::with(['pembeli', 'merch'])
            ->where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('status', 'like', "%{$search}%");
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $pembeliId, int $merchId, string $tanggalPenukaran): ?TransaksiMerch
    {
        return TransaksiMerch::where('pembeli_id', $pembeliId)
            ->where('merch_id', $merchId)
            ->where('tanggal_penukaran', $tanggalPenukaran)
            ->first();
    }

    public function create(array $data): TransaksiMerch
    {
        return TransaksiMerch::create($data);
    }

    public function update(int $pembeliId, int $merchId, string $tanggalPenukaran, array $data): TransaksiMerch
    {
        $transaksiMerch = TransaksiMerch::where('pembeli_id', $pembeliId)
            ->where('merch_id', $merchId)
            ->where('tanggal_penukaran', $tanggalPenukaran)
            ->firstOrFail();
        $transaksiMerch->update($data);
        return $transaksiMerch;
    }

    public function delete(int $pembeliId, int $merchId, string $tanggalPenukaran): bool
    {
        return TransaksiMerch::where('pembeli_id', $pembeliId)
            ->where('merch_id', $merchId)
            ->where('tanggal_penukaran', $tanggalPenukaran)
            ->delete() > 0;
    }
}