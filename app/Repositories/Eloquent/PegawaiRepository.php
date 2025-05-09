<?php

namespace App\Repositories\Eloquent;

use App\Models\Pegawai;
use App\Repositories\Interfaces\PegawaiRepositoryInterface;

class PegawaiRepository implements PegawaiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1, ): array
    {
        return Pegawai::where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('nama', 'like', '%' . $search . '%');
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }


    public function find($id)
    {
        return Pegawai::findOrFail($id);
    }

    public function create(array $data)
    {
        return Pegawai::create($data);
    }

    public function update($id, array $data)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($data);
        return $pegawai;
    }

    public function delete($id)
    {
        return Pegawai::destroy($id);
    }
}