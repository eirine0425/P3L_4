<?php
namespace App\UseCases\Garansi;

use App\Repositories\Garansi\GaransiRepositoryInterface;
use App\DTOs\Garansi\CreateGaransiRequest;
use App\DTOs\Garansi\UpdateGaransiRequest;

class GaransiUseCase
{
    public function __construct(
        protected GaransiRepositoryInterface $repository
    ) {}

    // Get all warranty records
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Find a warranty by ID (method updated to findById)
    public function find($id)
    {
        return $this->repository->findById($id); // Call the findById method
    }

    // Create a new warranty record
    public function create(CreateGaransiRequest $request)
    {
        // Prepare data from the request
        $data = $request->only([
            'status',
            'tanggal_aktif',
            'tanggal_berakhir',
        ]);

        return $this->repository->create($data);
    }

    // Update a warranty by ID
    public function update($id, UpdateGaransiRequest $request)
    {
        // Find the existing warranty
        $garansi = $this->repository->findById($id); // Using findById

        if (!$garansi) {
            return null;
        }

        // Prepare updated data from the request
        $data = $request->only([
            'status',
            'tanggal_aktif',
            'tanggal_berakhir',
        ]);

        return $this->repository->update($id, $data);
    }

    // Delete a warranty record by ID
    public function delete($id): bool
    {
        // Find the existing warranty
        $garansi = $this->repository->findById($id); // Using findById

        if (!$garansi) {
            return false;
        }

        // Delete the warranty
        return $this->repository->delete($id);
    }
}
