<?php

namespace App\DTOs\DiskusiProduk;

use Illuminate\Foundation\Http\FormRequest;

class CreateDiskusiProdukRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pembeli_id' => 'required|integer|exists:pembeli,pembeli_id',
            'barang_id' => 'required|integer|exists:barang,barang_id',
            'pertanyaan' => 'required|string',
            'jawaban' => 'nullable|string',
            'tanggal_diskusi' => 'nullable|date',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}