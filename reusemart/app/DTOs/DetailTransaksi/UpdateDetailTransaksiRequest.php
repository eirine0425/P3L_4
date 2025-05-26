<?php

namespace App\DTOs\DetailTransaksi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDetailTransaksiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'subtotal' => 'sometimes|numeric|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}