<?php

namespace App\DTOs\Pegawai;

use Illuminate\Http\Request;

class CreatePegawaiRequest
{
    public $user_id;
    public $nama_jabatan;
    public $tanggal_bergabung;
    public $nominal_komisi;
    public $status_aktif;
    public $nama;

    public function __construct(Request $request)
    {
        $this->user_id = $request->input('user_id');
        $this->nama_jabatan = $request->input('nama_jabatan');
        $this->tanggal_bergabung = $request->input('tanggal_bergabung');
        $this->nominal_komisi = $request->input('nominal_komisi');
        $this->status_aktif = $request->input('status_aktif');
        $this->nama = $request->input('nama');
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'nama_jabatan' => $this->nama_jabatan,
            'tanggal_bergabung' => $this->tanggal_bergabung,
            'nominal_komisi' => $this->nominal_komisi,
            'status_aktif' => $this->status_aktif,
            'nama' => $this->nama,
        ];
    }
}