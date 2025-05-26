<?php

namespace App\DTOs\DetailTransaksi;

use Illuminate\Foundation\Http\FormRequest;

class CreateDetailTransaksiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'barang_id' => 'required|integer|exists:barang,barang_id',
            'transaksi_id' => 'required|integer|exists:transaksi,transaksi_id',
            'subtotal' => 'required|numeric|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}