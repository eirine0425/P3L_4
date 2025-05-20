<?php

namespace App\DTOs\Komisi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKomisiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'persentase' => 'sometimes|numeric|min:0|max:100',
            'nominal_komisi' => 'sometimes|numeric|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}