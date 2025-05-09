<?php

namespace App\Repositories\Eloquent;

use App\Models\Penitip;
use App\Repositories\Interfaces\PenitipRepositoryInterface;

class PenitipRepository implements PenitipRepositoryInterface
{
    public function getAll(): array
    {
        return Penitip::all()->toArray();
    }

    public function find(int $id): ?Penitip
    {
        return Penitip::where('penitip_id', $id)->first(); // Menggunakan penitip_id
    }

    public function create(array $data): Penitip
    {
        return Penitip::create($data);
    }

    public function update(int $id, array $data): Penitip
    {
        $penitip = $this->find($id);
        if ($penitip) {
            $penitip->update($data);
        }
        return $penitip;
    }

    public function delete(int $id): bool
    {
        $penitip = $this->find($id);
        if ($penitip) {
            return $penitip->delete();
        }
        return false;
    }
}