<?php

namespace App\UseCases\KeranjangBelanja;

use App\Repositories\Interfaces\KeranjangBelanjaRepositoryInterface;
use App\DTOs\KeranjangBelanja\CreateKeranjangBelanjaRequest;
use App\DTOs\KeranjangBelanja\UpdateKeranjangBelanjaRequest;
use App\DTOs\KeranjangBelanja\GetKeranjangBelanjaPaginationRequest;

class KeranjangBelanjaUseCase
{
    public function __construct(
        protected KeranjangBelanjaRepositoryInterface $repository
    ) {}

    public function getAll(GetKeranjangBelanjaPaginationRequest $request): array
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

    public function create(CreateKeranjangBelanjaRequest $request)
    {
        $data = $request->only([
            'barang_id',
            'pembeli_id',
        ]);

        return $this->repository->create($data);
    }

    public function update($id, UpdateKeranjangBelanjaRequest $request)
    {
        $keranjangBelanja = $this->repository->find($id);

        if (!$keranjangBelanja) {
            return null;
        }

        $data = $request->only([
            'barang_id',
            'pembeli_id',
        ]);

        return $this->repository->update($id, $data);
    }

    public function delete($id): bool
    {
        $keranjangBelanja = $this->repository->find($id);

        if (!$keranjangBelanja) {
            return false;
        }

        return $this->repository->delete($id);
    }
}