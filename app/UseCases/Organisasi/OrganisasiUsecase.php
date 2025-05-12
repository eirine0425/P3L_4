<?php

namespace App\UseCases\Organisasi;

use App\DTOs\Organisasi\CreateOrganisasiRequest;
use App\DTOs\Organisasi\UpdateOrganisasiRequest;
use App\Models\Organisasi;
use App\Repositories\Interfaces\OrganisasiRepositoryInterface;
use App\DTOs\Organisasi\GetOrganisasiPaginationRequest;

class OrganisasiUsecase
{
    public function __construct(
        protected OrganisasiRepositoryInterface $repository
    ) {}

    public function getAll(GetOrganisasiPaginationRequest $request): array
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


    public function create(CreateOrganisasiRequest $request)
    {
        $data = $request->validated();
        return $this->repository->create($data);
    }


    public function update($id, UpdateOrganisasiRequest $request)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return null;
        }

        $organisasi->update($request->validated());

        return $organisasi;
    }

    public function delete($id): bool
    {
        $organisasi = $this->repository->find($id);

        if (!$organisasi) {
            return false;
        }

        return $this->repository->delete($id);
    }
}