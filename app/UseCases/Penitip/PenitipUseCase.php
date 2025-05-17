<?php

namespace App\UseCases\Penitip;

use App\Models\Penitip;
use App\DTOs\Penitip\CreatePenitipRequest;
use App\DTOs\Penitip\UpdatePenitipRequest;
use App\DTOs\Penitip\GetPenitipPaginationRequest;

class PenitipUseCase
{
    public function getAll(GetPenitipPaginationRequest $request)
    {
        return Penitip::query()
            ->when($request->getSearch(), function ($query) use ($request) {
                return $query->where('nama', 'like', '%' . $request->getSearch() . '%');
            })
            ->paginate($request->getPerPage());
    }

    public function create(CreatePenitipRequest $request)
    {
        return Penitip::create($request->toArray());
    }

    public function find($id)
    {
        return Penitip::find($id);
    }

    public function update(UpdatePenitipRequest $request, $id)
    {
        $penitip = Penitip::find($id);
        if ($penitip) {
            $penitip->update($request->validated());
            return $penitip;
        }
        return null;
    }

    public function delete($id)
    {
        $penitip = Penitip::find($id);
        if ($penitip) {
            $penitip->delete();
            return true;
        }
    }
}