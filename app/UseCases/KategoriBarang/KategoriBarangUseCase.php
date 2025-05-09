<?php
namespace App\UseCases\KategoriBarang;

use App\DTOs\KategoriBarang\CreateKategoriBarangRequest;
use App\DTOs\KategoriBarang\UpdateKategoriBarangRequest;
use App\Repositories\Interfaces\KategoriBarangRepositoryInterface;

class KategoriBarangUseCase
{
    public function __construct(
        protected KategoriBarangRepositoryInterface $repository
    ) {}

    // Retrieve all kategori barang
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Find a specific kategori barang by ID
    public function find($id)
    {
        return $this->repository->find($id); // Find kategori barang by kategori_id
    }

    // Create a new kategori barang
    public function create(CreateKategoriBarangRequest $request)
    {
        $data = $request->toArray([ // Mapping data for create
            'kategori_id',
            'nama_kategori',
            'deskripsi',
        ]);

        return $this->repository->create($data);
    }

    // Update a specific kategori barang
    public function update(UpdateKategoriBarangRequest $request, $id)
    {
        $kategoriBarang = $this->repository->find($id);
        if (!$kategoriBarang) {
            return null; // If kategori not found, return null
        }

        $data = $request->validated(); // Use validated data for update

        return $this->repository->update($id, $data); // Update based on kategori_id
    }

    // Delete a specific kategori barang by ID
    public function delete($id): bool
    {
        $kategoriBarang = $this->repository->find($id); // Find kategori barang by kategori_id
        if (!$kategoriBarang) {
            return false; // If kategori not found, return false
        }

        return $this->repository->delete($id); // Delete kategori barang
    }
}