<?php

namespace App\UseCases\Alamat;

use App\DTOs\Alamat\CreateAlamatRequest;
use App\DTOs\Alamat\GetAlamatPaginationRequest;
use App\DTOs\Alamat\UpdateAlamatRequest;
use App\Models\Alamat;
use App\Repositories\Interfaces\AlamatRepositoryInterface;
use App\DTOs\Organisasi\GetOrganisasiPaginationRequest;

class AlamatUsecase
{
    public function __construct(
        protected AlamatRepositoryInterface $repository
    ) {}

    public function getAll(GetAlamatPaginationRequest $request): array
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