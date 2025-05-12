<?php

namespace App\DTOs\Alamat;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlamatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah sesuai kebutuhan otorisasi Anda
    }

    public function rules(): array
    {
        return [
            'pembeli_id'     => 'sometimes|required|integer',
            'alamat'         => 'sometimes|required|string|max:255',
            'kode_pos'       => 'sometimes|required|string|max:10',
            'status_default' => 'sometimes|required|string|in:Y,N',
        ];
    }

}