<?php

namespace App\UseCases\Barang;

use App\Repositories\Interfaces\BarangRepositoryInterface;
use App\DTOs\Barang\CreateBarangRequest;
use App\DTOs\Barang\UpdateBarangRequest;
use App\DTOs\Barang\GetBarangPaginationRequest;

class BarangUseCase
{
    public function __construct(
        protected BarangRepositoryInterface $repository
    ) {}

    public function getAll(GetBarangPaginationRequest $request): array
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

    public function create(CreateBarangRequest $request)
    {
        $data = $request->only([
            'penitip_id',
            'kategori_id',
            'status',
            'kondisi',
            'nama_barang',
            'harga',
            'rating',
            'deskripsi',
            'tanggal_penitipan',
        ]);

        return $this->repository->create($data);
    }

    public function update($id, UpdateBarangRequest $request)
    {
        $barang = $this->repository->find($id);

        if (!$barang) {
            return null;
        }

        $data = $request->only([
            'penitip_id',
            'kategori_id',
            'status',
            'kondisi',
            'nama_barang',
            'harga',
            'rating',
            'deskripsi',
            'tanggal_penitipan',
        ]);

        return $this->repository->update($id, $data);
    }

    public function delete($id): bool
    {
        $barang = $this->repository->find($id);

        if (!$barang) {
            return false;
        }

        return $this->repository->delete($id);
    }

    public function searchByPenitip(int $penitipId, GetBarangPaginationRequest $request): array
    {
        $filters = $request->getFilters();
        $filters['penitip_id'] = $penitipId;
        
        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            filters: $filters,
            page: $request->getPage()
        );
    }

    public function getAdvancedSearch(GetBarangPaginationRequest $request): array
    {
        return $this->repository->getAdvancedSearch(
            filters: $request->getFilters(),
            perPage: $request->getPerPage(),
            page: $request->getPage()
        );
    }

    public function getItemsSummary(int $penitipId): array
    {
        $statusCounts = $this->repository->getStatusCounts($penitipId);
        
        return [
            'total_items' => $statusCounts['semua_status'],
            'active_items' => $statusCounts['aktif'],
            'pending_verification' => $statusCounts['menunggu_verifikasi'],
            'sold_items' => $statusCounts['terjual'],
            'inactive_items' => $statusCounts['tidak_aktif'],
            'rejected_items' => $statusCounts['ditolak'],
        ];
    }
}