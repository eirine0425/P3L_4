<?php

namespace App\Repositories\Garansi;

use App\Models\Garansi;

class GaransiRepository implements GaransiRepositoryInterface
{
    public function getAll()
    {
        return Garansi::all();
    }

    public function findById($id)
    {
        return Garansi::find($id);
    }

    public function create(array $data)
    {
        return Garansi::create($data);
    }

    public function update($id, array $data)
    {
        $garansi = Garansi::find($id);
        if ($garansi) {
            $garansi->update($data);
        }
        return $garansi;
    }

    public function delete($id)
    {
        $garansi = Garansi::find($id);
        if ($garansi) {
            return $garansi->delete();
        }
        return false;
    }
}