<?php

namespace App\DTOs\Barang;

use Illuminate\Foundation\Http\FormRequest;

class CreateBarangRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'penitip_id' => 'required|integer',
            'kategori_id' => 'required|integer',
            'status' => 'required|string|in:terjual,belum_terjual,sold out',
            'kondisi' => 'required|string|in:baru,layak,sangat_layak',
            'nama_barang' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
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