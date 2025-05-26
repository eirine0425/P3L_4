<?php

namespace App\DTOs\KategoriBarang;

use Illuminate\Http\Request;

class CreateKategoriBarangRequest
{
    public int $kategori_id;
    public string $nama_kategori;
    public string $deskripsi;

    public function __construct(Request $request)
    {
        $this->nama_kategori = $request->input('nama_kategori');
        $this->deskripsi = $request->input('deskripsi');
    }

    public function toArray(): array
    {
        return [
            'nama_kategori' => $this->nama_kategori,
            'deskripsi'     => $this->deskripsi,
        ];
    }
}