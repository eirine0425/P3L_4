<?php

namespace App\Repositories\Eloquent;

use App\Models\RequestDonasi;
use App\Repositories\Interfaces\RequestDonasiRepositoryInterface;

class RequestDonasiRepository implements RequestDonasiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search ="", int $page = 1): array
    {
        return RequestDonasi::where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('deskripsi', 'like', '%' . $search . '%');
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function find(int $id): ?RequestDonasi
    {
        return RequestDonasi::where('request_id', $id)->first(); // Using request_id
    }

    public function create(array $data): RequestDonasi
    {
        return RequestDonasi::create($data);
    }

    public function update(int $id, array $data): RequestDonasi
    {
        $requestDonasi = $this->find($id);
        if ($requestDonasi) {
            $requestDonasi->update($data);
        }
        return $requestDonasi;
    }

    public function delete(int $id): bool
    {
        $requestDonasi = $this->find($id);
        if ($requestDonasi) {
            return $requestDonasi->delete();
        }
        return false;
    }
}
