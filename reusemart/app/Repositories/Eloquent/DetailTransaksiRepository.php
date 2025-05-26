<?php

namespace App\Repositories\Eloquent;

use App\Models\DetailTransaksi;
use App\Repositories\Interfaces\DetailTransaksiRepositoryInterface;

class DetailTransaksiRepository implements DetailTransaksiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1): array
    {
        return DetailTransaksi::with(['barang', 'transaksi'])
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $barangId, int $transaksiId): ?DetailTransaksi
    {
        return DetailTransaksi::where('barang_id', $barangId)
            ->where('transaksi_id', $transaksiId)
            ->first();
    }

    public function create(array $data): DetailTransaksi
    {
        return DetailTransaksi::create($data);
    }

    public function update(int $barangId, int $transaksiId, array $data): DetailTransaksi
    {
        $detailTransaksi = DetailTransaksi::where('barang_id', $barangId)
            ->where('transaksi_id', $transaksiId)
            ->firstOrFail();
        $detailTransaksi->update($data);
        return $detailTransaksi;
    }

    public function delete(int $barangId, int $transaksiId): bool
    {
        return DetailTransaksi::where('barang_id', $barangId)
            ->where('transaksi_id', $transaksiId)
            ->delete() > 0;
    }
}