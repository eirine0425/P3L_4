<?php

namespace App\DTOs\Pembeli;

use Illuminate\Http\Request;

class CreatePembeliRequest
{
    public int $pembeli_id;
    public string $nama;
    public int $user_id;
    public int $keranjang_id;
    public int $poin_loyalitas;
    public string $tanggal_registrasi;

    public function __construct(Request $request)
    {
        $this->pembeli_id = $request->input('pembeli_id');
        $this->nama = $request->input('nama');
        $this->user_id = $request->input('user_id');
        $this->keranjang_id = $request->input('keranjang_id');
        $this->poin_loyalitas = $request->input('poin_loyalitas');
        $this->tanggal_registrasi = $request->input('tanggal_registrasi');
    }

    public function toArray(): array
    {
        return [
            'pembeli_id'         => $this->pembeli_id,
            'nama'               => $this->nama,
            'user_id'            => $this->user_id,
            'keranjang_id'       => $this->keranjang_id,
            'poin_loyalitas'     => $this->poin_loyalitas,
            'tanggal_registrasi' => $this->tanggal_registrasi,
        ];
    }
}