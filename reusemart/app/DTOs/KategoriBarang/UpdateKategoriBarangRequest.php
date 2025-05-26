<?php

namespace App\DTOs\KategoriBarang;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKategoriBarangRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kategori_id'   => 'sometimes|required|integer',
            'nama_kategori' => 'sometimes|required|string|max:100',
            'deskripsi'     => 'sometimes|required|string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}