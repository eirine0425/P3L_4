<?php

namespace App\DTOs\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransaksiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pembeli_id' => 'required|integer|exists:pembeli,pembeli_id',
            'cs_id' => 'nullable|integer|exists:pegawai,pegawai_id',
            'tanggal_pelunasan' => 'nullable|date',
            'point_digunakan' => 'nullable|integer|min:0',
            'point_diperoleh' => 'nullable|integer|min:0',
            'bukti_pembayaran' => 'nullable|string|max:255',
            'metode_pengiriman' => 'nullable|string|max:100',
            'tanggal_pesan' => 'nullable|date',
            'total_harga' => 'required|numeric|min:0',
            'status_transaksi' => 'required|string|max:50',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}