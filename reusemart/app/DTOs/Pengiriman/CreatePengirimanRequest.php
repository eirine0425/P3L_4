<?php

namespace App\DTOs\Pengiriman;

use Illuminate\Foundation\Http\FormRequest;

class CreatePengirimanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pengirim_id' => 'required|integer|exists:pegawai,pegawai_id',
            'transaksi_id' => 'required|integer|exists:transaksi,transaksi_id',
            'alamat_id' => 'required|integer|exists:alamat,alamat_id',
            'status_pengiriman' => 'required|string|max:50',
            'tanggal_kirim' => 'nullable|date',
            'nama_penerima' => 'required|string|max:255',
            'tanggal_terima' => 'nullable|date',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}