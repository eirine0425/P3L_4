<?php

namespace App\UseCases\TransaksiPenitipan;

use App\Repositories\Interfaces\TransaksiPenitipanRepositoryInterface;
use App\DTOs\TransaksiPenitipan\CreateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\UpdateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\GetTransaksiPenitipanPaginationRequest;
use App\Repositories\TransaksiPenitipanRepository;
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
            'batas_penitipan',
            'tanggal_penitipan',
            'metode_penitipan',
            'status_perpanjangan',
            'status_penitipan',
        ]);

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
            'batas_penitipan',
            'tanggal_penitipan',
            'metode_penitipan',
            'status_perpanjangan',
            'status_penitipan',
        ]);

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

    public function extendPenitipan($id)
    {
        $transaksi = $this->repository->find($id);

        if (!$transaksi) {
            return null;
        }

        // Check if extension is already used
        if ($transaksi->status_perpanjangan == true) {
            return false; // Extension already used
        }

        // Add 30 days to batas_penitipan
        $batasPenitipan = Carbon::parse($transaksi->batas_penitipan);
        $batasPenitipan->addDays(30);

        $data = [
            'batas_penitipan' => $batasPenitipan->format('Y-m-d'),
            'status_perpanjangan' => true
        ];

        return $this->repository->update($id, $data);
    }
}