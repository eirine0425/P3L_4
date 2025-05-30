<?php

namespace App\Repositories\Eloquent;

use App\Models\TransaksiPenitipan;
use App\Repositories\Interfaces\TransaksiPenitipanRepositoryInterface;

class TransaksiPenitipanRepository implements TransaksiPenitipanRepositoryInterface
{
    protected $model;

    public function __construct(TransaksiPenitipan $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $transaksi = $this->find($id);
        if ($transaksi) {
            $transaksi->update($data);
            return $transaksi->fresh(); // Return updated model
        }
        return null;
    }

    public function delete($id)
    {
        $transaksi = $this->find($id);
        if ($transaksi) {
            return $transaksi->delete();
        }
        return false;
    }

    public function getAll($perPage = 15, $page = 1, $search = null)
    {
        $query = $this->model->with(['penitip', 'barang']);
        
        if ($search) {
            $query->whereHas('penitip', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })->orWhereHas('barang', function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate($perPage, ['*'], 'page', $page)->toArray();
    }
}