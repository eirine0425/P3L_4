<?php

namespace App\DTOs\TransaksiPenitipan;

use Illuminate\Foundation\Http\FormRequest;

class ExtendTransaksiPenitipanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'transaksi_penitipan_id' => 'required|integer|exists:transaksi_penitipan,transaksi_penitipan_id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'transaksi_penitipan_id.required' => 'ID transaksi penitipan harus diisi',
            'transaksi_penitipan_id.exists' => 'Transaksi penitipan tidak ditemukan',
        ];
    }
}
