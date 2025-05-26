<?php

namespace App\DTOs\DiskusiProduk;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiskusiProdukRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pembeli_id' => 'sometimes|integer|exists:pembeli,pembeli_id',
            'barang_id' => 'sometimes|integer|exists:barang,barang_id',
            'pertanyaan' => 'sometimes|string',
            'jawaban' => 'nullable|string',
            'tanggal_diskusi' => 'nullable|date',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}