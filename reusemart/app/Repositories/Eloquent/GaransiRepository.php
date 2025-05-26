<?php

namespace App\Repositories\Eloquent;

use App\Models\Garansi;
use App\Repositories\Interfaces\GaransiRepositoryInterface;

class GaransiRepository implements GaransiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search = "", int $page = 1, ): array
    {
        return Garansi::where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('status', 'like', '%' . $search . '%');
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?Garansi
    {
        return Garansi::find($id);
    }

    public function create(array $data): Garansi
    {
        return Garansi::create($data);
    }

    public function update(int $id, array $data): ?Garansi
    {
        $garansi = Garansi::find($id);
        if ($garansi) {
            $garansi->update($data);
        }
        return $garansi;
    }

    public function delete(int $id): bool
    {
        $garansi = Garansi::find($id);
        if ($garansi) {
            return $garansi->delete();
        }
        return false;
    }
}