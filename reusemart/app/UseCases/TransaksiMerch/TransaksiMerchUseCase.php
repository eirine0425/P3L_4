<?php

namespace App\UseCases\TransaksiMerch;

use App\Repositories\Interfaces\TransaksiMerchRepositoryInterface;
use App\DTOs\TransaksiMerch\CreateTransaksiMerchRequest;
use App\DTOs\TransaksiMerch\UpdateTransaksiMerchRequest;
use App\DTOs\TransaksiMerch\GetTransaksiMerchPaginationRequest;

class TransaksiMerchUseCase
{
    public function __construct(
        protected TransaksiMerchRepositoryInterface $repository
    ) {}

    public function getAll(GetTransaksiMerchPaginationRequest $request): array
    {
        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch()
        );
    }

    public function find(string $id)
    {
        [$pembeliId, $merchId, $tanggalPenukaran] = explode('|', $id);
        return $this->repository->find((int)$pembeliId, (int)$merchId, $tanggalPenukaran);
    }

    public function create(CreateTransaksiMerchRequest $request)
    {
        $data = $request->only([
            'pembeli_id',
            'merch_id',
            'tanggal_penukaran',
            'status',
        ]);

        return $this->repository->create($data);
    }

    public function update(string $id, UpdateTransaksiMerchRequest $request)
    {
        [$pembeliId, $merchId, $tanggalPenukaran] = explode('|', $id);

        $transaksiMerch = $this->repository->find((int)$pembeliId, (int)$merchId, $tanggalPenukaran);

        if (!$transaksiMerch) {
            return null;
        }

        $data = $request->only(['status']);

        return $this->repository->update((int)$pembeliId, (int)$merchId, $tanggalPenukaran, $data);
    }

    public function delete(string $id): bool
    {
        [$pembeliId, $merchId, $tanggalPenukaran] = explode('|', $id);

        $transaksiMerch = $this->repository->find((int)$pembeliId, (int)$merchId, $tanggalPenukaran);

        if (!$transaksiMerch) {
            return false;
        }

        return $this->repository->delete((int)$pembeliId, (int)$merchId, $tanggalPenukaran);
    }
}
