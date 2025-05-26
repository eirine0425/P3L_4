<?php

namespace App\Repositories\Eloquent;

use App\Models\Organisasi;
use App\Repositories\Interfaces\OrganisasiRepositoryInterface;

class OrganisasiRepository implements OrganisasiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1, ): array
    {
        return Organisasi::where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('nama', 'like', '%' . $search . '%');
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?Organisasi
    {
        return Organisasi::find($id);
    }

    public function create(array $data): Organisasi
    {
        return Organisasi::create($data);
    }

    public function update(int $id, array $data): Organisasi
    {
        $organisasi = Organisasi::findOrFail($id);
        $organisasi->update($data);
        return $organisasi;
    }

    public function delete(int $id): bool
    {
        return Organisasi::destroy($id) > 0;
    }
}