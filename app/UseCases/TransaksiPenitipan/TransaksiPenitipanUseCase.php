<?php

namespace App\UseCases\TransaksiPenitipan;

use App\Repositories\Interfaces\TransaksiPenitipanRepositoryInterface;
use App\DTOs\TransaksiPenitipan\CreateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\UpdateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\GetTransaksiPenitipanPaginationRequest;
use Carbon\Carbon;

class TransaksiPenitipanUseCase
{
    public function __construct(
        protected TransaksiPenitipanRepositoryInterface $repository
    ) {}

    public function getAll(GetTransaksiPenitipanPaginationRequest $request): array
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

    public function create(CreateTransaksiPenitipanRequest $request)
    {
        $data = $request->only([
            'penitip_id',
            'barang_id',
            'tanggal_penitipan',
            'metode_penitipan',
            'status_perpanjangan',
            'status_penitipan',
        ]);

        // Set tanggal_penitipan to current datetime if not provided
        if (!isset($data['tanggal_penitipan']) || empty($data['tanggal_penitipan'])) {
            $data['tanggal_penitipan'] = now();
        }

        // batas_penitipan will be automatically calculated in the model's boot method

        return $this->repository->create($data);
    }

    public function update($id, UpdateTransaksiPenitipanRequest $request)
    {
        $transaksiPenitipan = $this->repository->find($id);

        if (!$transaksiPenitipan) {
            return null;
        }

        $data = $request->only([
            'penitip_id',
            'barang_id',
            'tanggal_penitipan',
            'metode_penitipan',
            'status_perpanjangan',
            'status_penitipan',
        ]);

        // Remove batas_penitipan from manual updates as it will be auto-calculated
        // when tanggal_penitipan changes

        return $this->repository->update($id, $data);
    }

    public function delete($id): bool
    {
        $transaksiPenitipan = $this->repository->find($id);

        if (!$transaksiPenitipan) {
            return false;
        }

        return $this->repository->delete($id);
    }

    public function getExpiringConsignments($days = 7): array
    {
        return $this->repository->getExpiringConsignments($days);
    }

    public function getExpiredConsignments(): array
    {
        return $this->repository->getExpiredConsignments();
    }

    public function getConsignmentsByStatus(string $statusDurasi): array
    {
        return $this->repository->getConsignmentsByStatus($statusDurasi);
    }

    public function extendConsignment($id, int $additionalDays = 30)
    {
        $transaksiPenitipan = $this->repository->find($id);

        if (!$transaksiPenitipan) {
            return null;
        }

        $currentBatas = $transaksiPenitipan->batas_penitipan 
            ? Carbon::parse($transaksiPenitipan->batas_penitipan)
            : Carbon::parse($transaksiPenitipan->tanggal_penitipan)->addDays(30);

        $newBatas = $currentBatas->addDays($additionalDays);

        $data = [
            'batas_penitipan' => $newBatas,
            'status_perpanjangan' => 'diperpanjang'
        ];

        return $this->repository->update($id, $data);
    }
}
