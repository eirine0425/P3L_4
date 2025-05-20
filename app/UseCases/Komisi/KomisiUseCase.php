<?php

namespace App\UseCases\Komisi;

use App\Repositories\Interfaces\KomisiRepositoryInterface;
use App\DTOs\Komisi\CreateKomisiRequest;
use App\DTOs\Komisi\UpdateKomisiRequest;
use App\DTOs\Komisi\GetKomisiPaginationRequest;

class KomisiUseCase
{
    public function __construct(
        protected KomisiRepositoryInterface $repository
    ) {}

    public function getAll(GetKomisiPaginationRequest $request): array
    {
        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch()
        );
    }

    public function find($id)
    {
        [$pegawaiId, $penitipId, $barangId] = explode('-', $id);
        return $this->repository->find((int)$pegawaiId, (int)$penitipId, (int)$barangId);
    }

    public function create(CreateKomisiRequest $request)
    {
        $data = $request->only([
            'pegawai_id',
            'penitip_id',
            'barang_id',
            'jumlah_komisi',
            'tanggal_komisi',
        ]);

        return $this->repository->create($data);
    }

    public function update($id, UpdateKomisiRequest $request)
    {
        [$pegawaiId, $penitipId, $barangId] = explode('-', $id);

        $komisi = $this->repository->find((int)$pegawaiId, (int)$penitipId, (int)$barangId);

        if (!$komisi) {
            return null;
        }

        $data = $request->only([
            'jumlah_komisi',
            'tanggal_komisi',
        ]);

        return $this->repository->update((int)$pegawaiId, (int)$penitipId, (int)$barangId, $data);
    }

    public function delete($id): bool
    {
        [$pegawaiId, $penitipId, $barangId] = explode('-', $id);

        $komisi = $this->repository->find((int)$pegawaiId, (int)$penitipId, (int)$barangId);

        if (!$komisi) {
            return false;
        }

        return $this->repository->delete((int)$pegawaiId, (int)$penitipId, (int)$barangId);
    }
}
