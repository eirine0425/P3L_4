<?php

namespace App\DTOs\TransaksiPenitipan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransaksiPenitipanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'penitip_id' => 'sometimes|integer|exists:penitip,penitip_id',
            'barang_id' => 'sometimes|integer|exists:barang,barang_id',
            'batas_penitipan' => 'sometimes|date',
            'tanggal_penitipan' => 'nullable|date',
            'metode_penitipan' => 'sometimes|string|max:100',
            'status_perpanjangan' => 'sometimes|boolean',
            'status_penitipan' => 'sometimes|string|max:50',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}