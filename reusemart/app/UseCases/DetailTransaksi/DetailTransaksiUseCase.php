<?php

namespace App\UseCases\DetailTransaksi;

use App\Repositories\Interfaces\DetailTransaksiRepositoryInterface;
use App\DTOs\DetailTransaksi\CreateDetailTransaksiRequest;
use App\DTOs\DetailTransaksi\UpdateDetailTransaksiRequest;
use App\DTOs\DetailTransaksi\GetDetailTransaksiPaginationRequest;

class DetailTransaksiUseCase
{
    public function __construct(
        protected DetailTransaksiRepositoryInterface $repository
    ) {}

    public function getAll(GetDetailTransaksiPaginationRequest $request): array
    {
        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch()
        );
    }

    public function find($barangId, $transaksiId)
    {
        return $this->repository->find($barangId, $transaksiId);
    }

    public function create(CreateDetailTransaksiRequest $request)
    {
        $data = $request->only([
            'barang_id',
            'transaksi_id',
            'subtotal',
        ]);

        return $this->repository->create($data);
    }

    public function update($barangId, $transaksiId, UpdateDetailTransaksiRequest $request)
    {
        $detailTransaksi = $this->repository->find($barangId, $transaksiId);

        if (!$detailTransaksi) {
            return null;
        }

        $data = $request->only([
            'subtotal',
        ]);

        return $this->repository->update($barangId, $transaksiId, $data);
    }

    public function delete($barangId, $transaksiId): bool
    {
        $detailTransaksi = $this->repository->find($barangId, $transaksiId);

        if (!$detailTransaksi) {
            return false;
        }

        return $this->repository->delete($barangId, $transaksiId);
    }
}