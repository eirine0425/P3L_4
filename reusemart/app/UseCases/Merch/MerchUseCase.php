<?php

namespace App\UseCases\Merch;

use App\Repositories\Interfaces\MerchRepositoryInterface;
use App\DTOs\Merch\CreateMerchRequest;
use App\DTOs\Merch\UpdateMerchRequest;
use App\DTOs\Merch\GetMerchPaginationRequest;

class MerchUseCase
{
    public function __construct(
        protected MerchRepositoryInterface $repository
    ) {}

    // Ambil semua data merch
    public function getAll(GetMerchPaginationRequest $request): array
    {

        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch()
        );
    }

    // Temukan merch berdasarkan ID
    public function find($id)
    {
        return $this->repository->find($id);
    }

    // Buat data merch baru
    public function create(CreateMerchRequest $request)
    {
        // Ambil data yang diperlukan dari request
        $data = $request->only([
            'nama',
            'jumlah_poin',
            'stock_merch',
        ]);

        return $this->repository->create($data);
    }

    // Perbarui data merch berdasarkan ID
    public function update($id, UpdateMerchRequest $request)
    {
        $merch = $this->repository->find($id);

        if (!$merch) {
            return null;
        }

        $data = $request->only([
            'nama',
            'jumlah_poin',
            'stock_merch',
        ]);

        // Menggunakan objek merch untuk update
        return $this->repository->update($merch, $data);
    }

    // Hapus data merch berdasarkan ID
    public function delete($id): bool
    {
        $merch = $this->repository->find($id);

        if (!$merch) {
            return false;
        }

        return $this->repository->delete($merch);
    }
}