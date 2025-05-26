<?php

namespace App\DTOs\Barang;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarangRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'penitip_id' => 'sometimes|required|integer',
            'kategori_id' => 'sometimes|required|integer',
            'status' => 'sometimes|required|string|in:terjual,belum_terjual,sold out',
            'kondisi' => 'sometimes|required|string|in:baru,layak,sangat_layak',
            'nama_barang' => 'sometimes|required|string|max:255',
            'harga' => 'sometimes|required|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'deskripsi' => 'nullable|string',
            'tanggal_penitipan' => 'nullable|date',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}