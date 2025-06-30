<?php

namespace App\DTOs\Organisasi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganisasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah sesuai kebutuhan otorisasi Anda
    }

    public function rules(): array
    {
        return [
            'nama_organisasi' => 'sometimes|required|string|max:255',
            'alamat' => 'sometimes|required|string|max:255',
            'user_id'         => 'sometimes|required|integer',
        ];
    }    
}