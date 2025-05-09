<?php

namespace App\DTOs\Donasi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDonasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barang_id' => 'nullable|integer|exists:barang,id',
            'deskripsi' => 'nullable|string',
            'nama_kategori' => 'sometimes|required|string|max:255',
            'nama_penerima' => 'sometimes|required|string|max:255',
        ];
    }
}
