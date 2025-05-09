<?php

namespace App\UseCases\Donasi;

use App\Repositories\Interfaces\DonasiRepositoryInterface;
use App\DTOs\Donasi\CreateDonasiRequest;
use App\DTOs\Donasi\UpdateDonasiRequest;

class DonasiUseCase
{
    public function __construct(
        protected DonasiRepositoryInterface $repository
    ) {}

    // Get all donation records
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Find a donation by ID
    public function find($id)
    {
        return $this->repository->find($id);
    }

    // Create a new donation record
    public function create(CreateDonasiRequest $request)
    {
        // Prepare data from the request
        $data = $request->only([
            'barang_id',
            'deskripsi',
            'nama_kategori',
            'nama_penerima',
        ]);

        return $this->repository->create($data);
    }

    // Update a donation by ID
    public function update($id, UpdateDonasiRequest $request)
    {
        // Find the existing donation
        $donasi = $this->repository->find($id);

        if (!$donasi) {
            return null;
        }

        // Prepare updated data from the request
        $data = $request->only([
            'barang_id',
            'deskripsi',
            'nama_kategori',
            'nama_penerima',
        ]);

        return $this->repository->update($id, $data);
    }

    // Delete a donation record by ID
    public function delete($id): bool
    {
        // Find the existing donation
        $donasi = $this->repository->find($id);

        if (!$donasi) {
            return false;
        }

        // Delete the donation
        return $this->repository->delete($id);
    }
}