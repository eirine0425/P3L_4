<?php

namespace App\DTOs\Alamat;

use Illuminate\Foundation\Http\FormRequest;

class CreateAlamatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah sesuai kebutuhan otorisasi Anda
    }

    public function rules(): array
    {
        return [
            'pembeli_id'     => 'required|integer',
            'alamat'         => 'required|string|max:255',
            'kode_pos'       => 'required|string|max:10',
            'status_default' => 'required|string|in:Y,N',
        ];
    }
}