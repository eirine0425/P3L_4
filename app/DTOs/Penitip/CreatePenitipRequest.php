<?php

namespace App\DTOs\Penitip;

use Illuminate\Http\Request;

class CreatePenitipRequest
{
    public int $penitip_id;
    public string $nama;
    public int $point_reward;
    public string $tanggal_registrasi;
    public string $no_ktp;
    public int $user_id;
    public ?string $badge;
    public ?string $periode;

    public function __construct(Request $request)
    {
        $this->penitip_id = $request->input('penitip_id');
        $this->nama = $request->input('nama');
        $this->point_reward = $request->input('point_reward');
        $this->tanggal_registrasi = $request->input('tanggal_registrasi');
        $this->no_ktp = $request->input('no_ktp');
        $this->user_id = $request->input('user_id');
        $this->badge = $request->input('badge');
        $this->periode = $request->input('periode');
    }

    public function toArray(): array
    {
        return [
            'penitip_id'         => $this->penitip_id,
            'nama'               => $this->nama,
            'point_reward'       => $this->point_reward,
            'tanggal_registrasi' => $this->tanggal_registrasi,
            'no_ktp'             => $this->no_ktp,
            'user_id'            => $this->user_id,
            'badge'              => $this->badge,
            'periode'            => $this->periode,
        ];
    }
}