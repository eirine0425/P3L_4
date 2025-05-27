<?php

namespace App\Repositories\Eloquent;

use App\Models\Barang;
use App\Repositories\Interfaces\BarangRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class BarangRepository implements BarangRepositoryInterface
{
    public function getAll(int $perPage = 10, array $filters = [], int $page = 1): array
    {
        return $this->buildQuery($filters)
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?Barang
    {
        return Barang::with(['penitip', 'kategori', 'garansi'])
            ->find($id);
    }

    public function create(array $data): Barang
    {
        return Barang::create($data);
    }

    public function update(int $id, array $data): Barang
    {
        $barang = Barang::findOrFail($id);
        $barang->update($data);
        return $barang;
    }

    public function delete(int $id): bool
    {
        return Barang::destroy($id) > 0;
    }

    public function searchByPenitip(int $penitipId, array $filters = [], int $perPage = 10, int $page = 1): array
    {
        $filters['penitip_id'] = $penitipId;
        return $this->getAll($perPage, $filters, $page);
    }

    public function getAdvancedSearch(array $filters = [], int $perPage = 10, int $page = 1): array
    {
        return $this->getAll($perPage, $filters, $page);
    }

    private function buildQuery(array $filters = []): Builder
{
    $query = Barang::query()
        ->with(['penitip', 'kategori', 'garansi'])
        ->select('barang.*', 'kategori_barang.nama_kategori')
        ->leftJoin('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id');

    // Search global: nama_barang, deskripsi, kategori, harga, kondisi, status
    if (!empty($filters['search'])) {
        $searchTerm = $filters['search'];
        $query->where(function ($q) use ($searchTerm) {
            $q->where('barang.nama_barang', 'like', "%{$searchTerm}%")
              ->orWhere('barang.deskripsi', 'like', "%{$searchTerm}%")
              ->orWhere('kategori_barang.nama_kategori', 'like', "%{$searchTerm}%")
              ->orWhereRaw("CAST(barang.harga AS CHAR) LIKE ?", ["%{$searchTerm}%"])
              ->orWhere('barang.kondisi', 'like', "%{$searchTerm}%")
              ->orWhere('barang.status', 'like', "%{$searchTerm}%");
        });
    }


        // Filter by status (Status dropdown)
        if (!empty($filters['status'])) {
            $query->where('barang.status', $filters['status']);
        }

        // Filter by penitip_id if provided (for specific penitip's items)
        if (!empty($filters['penitip_id'])) {
            $query->where('barang.penitip_id', $filters['penitip_id']);
        }

        // Default sorting by creation date (newest first)
        $query->orderBy('barang.created_at', 'desc');

        return $query;
    }

    public function getStatusCounts(int $penitipId = null): array
    {
        $query = Barang::query();
        
        if ($penitipId) {
            $query->where('penitip_id', $penitipId);
        }

        $statusCounts = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'semua_status' => array_sum($statusCounts),
            'menunggu_verifikasi' => $statusCounts['menunggu_verifikasi'] ?? 0,
            'aktif' => $statusCounts['aktif'] ?? 0,
            'tidak_aktif' => $statusCounts['tidak_aktif'] ?? 0,
            'terjual' => $statusCounts['terjual'] ?? 0,
            'ditolak' => $statusCounts['ditolak'] ?? 0,
        ];
    }
}
