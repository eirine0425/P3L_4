<?php

namespace App\DTOs\RequestDonasi;

use Illuminate\Http\Request;

class CreateRequestDonasiRequest
{
    public int $organisasi_id;
    public string $deskripsi;
    public string $tanggal_request;
    public string $status_request;

    public function __construct(Request $request)
    {
        $this->organisasi_id = $request->input('organisasi_id');
        $this->deskripsi = $request->input('deskripsi');
        $this->tanggal_request = $request->input('tanggal_request');
        $this->status_request = $request->input('status_request');
    }

    public function toArray(): array
    {
        return [
            'organisasi_id'    => $this->organisasi_id,
            'deskripsi'        => $this->deskripsi,
            'tanggal_request'  => $this->tanggal_request,
            'status_request'   => $this->status_request,
        ];
    }
}


