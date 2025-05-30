<?php

namespace App\UseCases\TransaksiPenitipan;

use App\Repositories\Interfaces\TransaksiPenitipanRepositoryInterface;
use App\DTOs\TransaksiPenitipan\CreateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\UpdateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\GetTransaksiPenitipanPaginationRequest;
use App\Repositories\TransaksiPenitipanRepository;
use Carbon\Carbon;
use App\Models\TransaksiPenitipan;
class TransaksiPenitipanUseCase
{
    public function __construct(
        protected TransaksiPenitipanRepositoryInterface $repository
    ) {}

    public function getAll(GetTransaksiPenitipanPaginationRequest $request): array
    {
        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch()
        );
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateTransaksiPenitipanRequest $request)
    {
        $data = $request->only([
            'penitip_id',
            'barang_id',
            'batas_penitipan',
            'tanggal_penitipan',
            'metode_penitipan',
            'status_perpanjangan',
            'status_penitipan',
        ]);

        return $this->repository->create($data);
    }

    public function update($id, UpdateTransaksiPenitipanRequest $request)
    {
        $transaksiPenitipan = $this->repository->find($id);

        if (!$transaksiPenitipan) {
            return null;
        }

        $data = $request->only([
            'penitip_id',
            'barang_id',
            'batas_penitipan',
            'tanggal_penitipan',
            'metode_penitipan',
            'status_perpanjangan',
            'status_penitipan',
        ]);

        return $this->repository->update($id, $data);
    }

    public function delete($id): bool
    {
        $transaksiPenitipan = $this->repository->find($id);

        if (!$transaksiPenitipan) {
            return false;
        }

        return $this->repository->delete($id);
    }

    public function extendPenitipan($id)
    {
        $transaksi = TransaksiPenitipan::find($id);

    if (!$transaksi || $transaksi->status_perpanjangan) {
        return null;
    }

    $transaksi->batas_penitipan = Carbon::parse($transaksi->batas_penitipan)->addDays(30);
    $transaksi->status_perpanjangan = true;
    $transaksi->save();

    return $transaksi;
    }
    public function getByPenitipId($penitipId)
{
    return TransaksiPenitipan::where('penitip_id', $penitipId)->get();
}

}