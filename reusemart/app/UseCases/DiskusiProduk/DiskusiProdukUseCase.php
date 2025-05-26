<?php

namespace App\UseCases\DiskusiProduk;

use App\Repositories\Interfaces\DiskusiProdukRepositoryInterface;
use App\DTOs\DiskusiProduk\CreateDiskusiProdukRequest;
use App\DTOs\DiskusiProduk\UpdateDiskusiProdukRequest;
use App\DTOs\DiskusiProduk\GetDiskusiProdukPaginationRequest;

class DiskusiProdukUseCase
{
    public function __construct(
        protected DiskusiProdukRepositoryInterface $repository
    ) {}

    public function getAll(GetDiskusiProdukPaginationRequest $request): array
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

    public function create(CreateDiskusiProdukRequest $request)
    {
        $data = $request->only([
            'pembeli_id',
            'barang_id',
            'pertanyaan',
            'jawaban',
            'tanggal_diskusi',
        ]);

        return $this->repository->create($data);
    }

    public function update($id, UpdateDiskusiProdukRequest $request)
    {
        $diskusiProduk = $this->repository->find($id);

        if (!$diskusiProduk) {
            return null;
        }

        $data = $request->only([
            'pembeli_id',
            'barang_id',
            'pertanyaan',
            'jawaban',
            'tanggal_diskusi',
        ]);

        return $this->repository->update($id, $data);
    }

    public function delete($id): bool
    {
        $diskusiProduk = $this->repository->find($id);

        if (!$diskusiProduk) {
            return false;
        }

        return $this->repository->delete($id);
    }
}