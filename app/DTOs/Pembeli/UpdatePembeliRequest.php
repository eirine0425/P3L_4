<?php

namespace App\DTOs\Pembeli;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePembeliRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama'               => 'sometimes|required|string|max:100',
            'user_id'            => 'sometimes|required|integer',
            'keranjang_id'       => 'sometimes|required|integer',
            'poin_loyalitas'     => 'sometimes|required|integer|min:0',
            'tanggal_registrasi' => 'sometimes|required|date',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}