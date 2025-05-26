<?php

namespace App\UseCases\Transaksi;

use App\Repositories\Interfaces\TransaksiRepositoryInterface;
use App\DTOs\Transaksi\CreateTransaksiRequest;
use App\DTOs\Transaksi\UpdateTransaksiRequest;
use App\DTOs\Transaksi\GetTransaksiPaginationRequest;

class TransaksiUseCase
{
    public function __construct(
        protected TransaksiRepositoryInterface $repository
    ) {}

    public function getAll(GetTransaksiPaginationRequest $request): array
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

    public function create(CreateTransaksiRequest $request)
    {
        $data = $request->only([
            'pembeli_id',
            'cs_id',
            'tanggal_pelunasan',
            'point_digunakan',
            'point_diperoleh',
            'bukti_pembayaran',
            'metode_pengiriman',
            'tanggal_pesan',
            'total_harga',
            'status_transaksi',
        ]);

        return $this->repository->create($data);
    }

    public function update($id, UpdateTransaksiRequest $request)
    {
        $transaksi = $this->repository->find($id);

        if (!$transaksi) {
            return null;
        }

        $data = $request->only([
            'pembeli_id',
            'cs_id',
            'tanggal_pelunasan',
            'point_digunakan',
            'point_diperoleh',
            'bukti_pembayaran',
            'metode_pengiriman',
            'tanggal_pesan',
            'total_harga',
            'status_transaksi',
        ]);

        return $this->repository->update($id, $data);
    }

    public function delete($id): bool
    {
        $transaksi = $this->repository->find($id);

        if (!$transaksi) {
            return false;
        }

        return $this->repository->delete($id);
    }
}