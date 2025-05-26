<?php

namespace App\DTOs\Pengiriman;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengirimanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pengirim_id' => 'sometimes|integer|exists:pegawai,pegawai_id',
            'transaksi_id' => 'sometimes|integer|exists:transaksi,transaksi_id',
            'alamat_id' => 'sometimes|integer|exists:alamat,alamat_id',
            'status_pengiriman' => 'sometimes|string|max:50',
            'tanggal_kirim' => 'nullable|date',
            'nama_penerima' => 'sometimes|string|max:255',
            'tanggal_terima' => 'nullable|date',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}