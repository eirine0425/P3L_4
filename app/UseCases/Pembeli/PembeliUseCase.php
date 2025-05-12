<?php
namespace App\UseCases\Pembeli;

use App\DTOs\Pembeli\CreatePembeliRequest;
use App\DTOs\Pembeli\UpdatePembeliRequest;
use App\Repositories\Interfaces\PembeliRepositoryInterface;
use App\DTOs\Pembeli\GetPembeliPaginationRequest;

class PembeliUseCase
{
    public function __construct(
        protected PembeliRepositoryInterface $repository
    ) {}

    public function getAll(GetPembeliPaginationRequest $request): array
    {

        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch()
        );
    }

    public function find($id)
    {
        return $this->repository->find($id); // Mencari pembeli berdasarkan pembeli_id
    }

    public function create(CreatePembeliRequest $request)
    {
        $data = $request->toArray([ // Menyusun data untuk create
            'pembeli_id',
            'nama',
            'user_id',
            'keranjang_id',
            'poin_loyalitas',
            'tanggal_registrasi',
        ]);
        return $this->repository->create($data);
    }

    public function update(UpdatePembeliRequest $request, $id)
    {
        $pembeli = $this->repository->find($id);
        if (!$pembeli) {
            return null; // Jika pembeli tidak ditemukan, return null
        }

        // Menggunakan validated() untuk mendapatkan data yang sudah divalidasi
        $data = $request->validated(); // Perbaikan: gunakan validated()

        return $this->repository->update($id, $data); // Melakukan update berdasarkan pembeli_id
    }

    public function delete($id): bool
    {
        $pembeli = $this->repository->find($id); // Mencari pembeli berdasarkan pembeli_id
        if (!$pembeli) {
            return false; // Jika pembeli tidak ditemukan
        }

        return $this->repository->delete($id); // Menghapus pembeli
    }
}