<?php

namespace App\DTOs\TransaksiPenitipan;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransaksiPenitipanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'penitip_id' => 'required|integer|exists:penitip,penitip_id',
            'barang_id' => 'required|integer|exists:barang,barang_id',
            'batas_penitipan' => 'required|date',
            'tanggal_penitipan' => 'nullable|date',
            'metode_penitipan' => 'required|string|max:100',
            'status_perpanjangan' => 'sometimes|boolean',
            'status_penitipan' => 'required|string|max:50',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}