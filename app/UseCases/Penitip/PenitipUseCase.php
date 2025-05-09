<?php

namespace App\UseCases\Penitip;

use App\DTOs\Penitip\CreatePenitipRequest;
use App\DTOs\Penitip\UpdatePenitipRequest;
use App\Repositories\Interfaces\PenitipRepositoryInterface;

class PenitipUseCase
{
    public function __construct(
        protected PenitipRepositoryInterface $repository
    ) {}

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function find($id)
    {
        return $this->repository->find($id); // Mencari penitip berdasarkan penitip_id
    }

    public function create(CreatePenitipRequest $request)
{
    $data = $request->toArray([
            'penitip_id',
            'nama',
            'point_reward',
            'tanggal_registrasi',
            'no_ktp',
            'user_id',
            'badge',
            'periode',
        ]);
        return $this->repository->create($data);
    }

    public function update(UpdatePenitipRequest $request, $id)
{
    $penitip = $this->repository->find($id);
    if (!$penitip) {
        return null;
    }

        $data = $request->only([
            'penitip_id',
            'nama',
            'point_reward',
            'tanggal_registrasi',
            'no_ktp',
            'user_id',
            'badge',
            'periode',
        ]);

        return $this->repository->update($id, $data); // Melakukan update berdasarkan penitip_id
    }

    public function delete($id): bool
    {
        $penitip = $this->repository->find($id); // Mencari penitip berdasarkan penitip_id
        if (!$penitip) {
            return false; // Jika penitip tidak ditemukan
        }
        return $this->repository->delete($id); // Menghapus penitip
    }
}