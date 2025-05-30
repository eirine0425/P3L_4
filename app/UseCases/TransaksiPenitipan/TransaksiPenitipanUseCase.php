<?php

namespace App\UseCases\TransaksiPenitipan;

use App\Repositories\TransaksiPenitipanRepository;
use Carbon\Carbon;

class TransaksiPenitipanUseCase
{
    protected $repository;

    public function __construct(TransaksiPenitipanRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    public function update($id, $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function extendPenitipan($id)
    {
        $transaksi = $this->repository->find($id);
        
        // Debug logging
        \Log::info("Attempting to extend penitipan for ID: {$id}");
        
        if (!$transaksi) {
            \Log::info("Transaksi not found for ID: {$id}");
            return null;
        }
        
        \Log::info("Current status_perpanjangan: " . ($transaksi->status_perpanjangan ? 'true' : 'false'));
        
        if ($transaksi->status_perpanjangan) {
            \Log::info("Extension already used for ID: {$id}");
            return null;
        }
        
        // Update batas penitipan dan status perpanjangan
        $newBatasPenitipan = Carbon::parse($transaksi->batas_penitipan)->addDays(30);
        
        $updatedData = [
            'batas_penitipan' => $newBatasPenitipan->format('Y-m-d'),
            'status_perpanjangan' => true
        ];
        
        \Log::info("Updating with data: ", $updatedData);
        
        $result = $this->repository->update($id, $updatedData);
        
        \Log::info("Update result: " . ($result ? 'success' : 'failed'));
        
        return $result;
    }
}
