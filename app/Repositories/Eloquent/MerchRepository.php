<?php
namespace App\Repositories\Eloquent;

use App\Models\Merch;
use App\Repositories\Interfaces\MerchRepositoryInterface;

class MerchRepository implements MerchRepositoryInterface
{
    // Mengambil semua data merch
    public function getAll(int $perPage = 10, int $page = 1, string $search = ""): array
    {
        return Merch::
            where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('nama', 'like', "%{$search}%");
                        
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    // Menemukan merch berdasarkan ID
    public function find($id)
    {
        return Merch::find($id); // Kembali objek merch jika ditemukan
    }

    // Membuat data merch baru
    public function create(array $data)
    {
        return Merch::create($data); // Membuat data merch baru
    }

    // Update dengan menerima objek merch
    public function update(Merch $merch, array $data)
    {
        $merch->update($data); // Perbarui data merch dengan data baru
        return $merch; // Mengembalikan objek merch yang sudah diperbarui
    }

    // Delete dengan menerima objek merch
    public function delete(Merch $merch)
    {
        return $merch->delete(); // Menghapus objek merch
    }
}