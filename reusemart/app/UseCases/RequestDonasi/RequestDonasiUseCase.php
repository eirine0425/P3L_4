<?php

namespace App\UseCases\RequestDonasi;

use App\DTOs\RequestDonasi\CreateRequestDonasiRequest;
use App\DTOs\RequestDonasi\UpdateRequestDonasiRequest;
use App\Repositories\Interfaces\RequestDonasiRepositoryInterface;
use App\DTOs\RequestDonasi\GetRequestDonasiPaginationRequest;

class RequestDonasiUseCase
{
    public function __construct(
        protected RequestDonasiRepositoryInterface $repository
    ) {}

     public function getAll(GetRequestDonasiPaginationRequest $request): array
    {

        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch()
        );
    }

    public function find($id)
    {
        return $this->repository->find($id); // Mencari request donasi berdasarkan request_id
    }

    public function create(CreateRequestDonasiRequest $request)
{
    $data = $request->toArray();
    return $this->repository->create($data);
}


    public function update(UpdateRequestDonasiRequest $request, $id)
    {
        $requestDonasi = $this->repository->find($id);
        if (!$requestDonasi) {
            return null; // Request not found, you could return a custom error message here
        }

        // Mengambil hanya field yang diperlukan
        $data = $request->only([
            'request_id',
            'organisasi_id',
            'deskripsi',
            'tanggal_request',
            'status_request',
        ]);

        return $this->repository->update($id, $data);
    }

    public function delete($id): bool
    {
        $requestDonasi = $this->repository->find($id);
        if (!$requestDonasi) {
            return false; // Request not found, return false
        }

        return $this->repository->delete($id); // Menghapus request donasi
    }
}
