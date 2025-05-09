<?php

namespace App\UseCases\Alamat;

use App\DTOs\Alamat\CreateAlamatRequest;
use App\DTOs\Alamat\UpdateAlamatRequest;
use App\Models\Alamat;
use App\Repositories\Interfaces\AlamatRepositoryInterface;

class AlamatUsecase
{
    public function __construct(
        protected AlamatRepositoryInterface $repository
    ) {}

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }


    public function create(CreateAlamatRequest $request)
    {
        $data = $request->validated();
        return $this->repository->create($data);
    }


    public function update($id, UpdateAlamatRequest $request)
    {
        $alamat = Alamat::find($id);
        if (!$alamat) {
            return null;
        }

        $alamat->update($request->validated());

        return $alamat;
    }

    public function delete($id): bool
    {
        $alamat = $this->repository->find($id);

        if (!$alamat) {
            return false;
        }

        return $this->repository->delete($id);
    }
}