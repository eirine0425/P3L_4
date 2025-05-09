<?php

namespace App\Repositories\Eloquent;

use App\Models\Organisasi;
use App\Repositories\Interfaces\OrganisasiRepositoryInterface;

class OrganisasiRepository implements OrganisasiRepositoryInterface
{
    public function getAll(): array
    {
        return Organisasi::all()->toArray();
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