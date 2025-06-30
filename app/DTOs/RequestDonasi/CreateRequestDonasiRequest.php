<?php

namespace App\DTOs\RequestDonasi;

use Illuminate\Http\Request;

class CreateRequestDonasiRequest
{
    public int $organisasi_id;
    public string $deskripsi;
    public string $tanggal_donasi;
    public string $status_request;

    public function __construct(Request $request)
    {
        $this->organisasi_id = $request->input('organisasi_id');
        $this->deskripsi = $request->input('deskripsi');
        $this->tanggal_donasi = $request->input('tanggal_donasi');
        $this->status_request = $request->input('status_request');
    }

    public function toArray(): array
    {
        return [
            'organisasi_id'    => $this->organisasi_id,
            'deskripsi'        => $this->deskripsi,
            'tanggal_donasi'  => $this->tanggal_donasi,
            'status_request'   => $this->status_request,
        ];
    }
}


