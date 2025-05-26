<?php

namespace App\UseCases\Pengiriman;

use App\Repositories\Interfaces\PengirimanRepositoryInterface;
use App\DTOs\Pengiriman\CreatePengirimanRequest;
use App\DTOs\Pengiriman\UpdatePengirimanRequest;
use App\DTOs\Pengiriman\GetPengirimanPaginationRequest;

class PengirimanUseCase
{
    public function __construct(
        protected PengirimanRepositoryInterface $repository
    ) {}

    public function getAll(GetPengirimanPaginationRequest $request): array
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

    public function create(CreatePengirimanRequest $request)
    {
        $data = $request->only([
            'pengirim_id',
            'transaksi_id',
            'alamat_id',
            'status_pengiriman',
            'tanggal_kirim',
            'nama_penerima',
            'tanggal_terima',
        ]);

        return $this->repository->create($data);
    }

    public function update($id, UpdatePengirimanRequest $request)
    {
        $pengiriman = $this->repository->find($id);

        if (!$pengiriman) {
            return null;
        }

        $data = $request->only([
            'pengirim_id',
            'transaksi_id',
            'alamat_id',
            'status_pengiriman',
            'tanggal_kirim',
            'nama_penerima',
            'tanggal_terima',
        ]);

        return $this->repository->update($id, $data);
    }

    public function delete($id): bool
    {
        $pengiriman = $this->repository->find($id);

        if (!$pengiriman) {
            return false;
        }

        return $this->repository->delete($id);
    }
}