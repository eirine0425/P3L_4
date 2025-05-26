<?php

namespace App\DTOs\Organisasi;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrganisasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah sesuai kebutuhan otorisasi Anda
    }

    public function rules(): array
    {
        return [
            'nama_organisasi' => 'required|string|max:255',
            'user_id'         => 'required|integer',
        ];
    }
}