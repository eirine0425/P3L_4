<?php

namespace App\DTOs\Donasi;

use Illuminate\Foundation\Http\FormRequest;

class CreateDonasiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'request_id' => 'nullable|integer',
            'barang_id' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
            'nama_kategori' => 'nullable|string|max:255',
            'nama_penerima' => 'required|string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}