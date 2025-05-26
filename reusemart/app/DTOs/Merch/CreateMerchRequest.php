<?php

namespace App\DTOs\Merch;

use Illuminate\Foundation\Http\FormRequest;

class CreateMerchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jumlah_poin' => 'required|integer',
            'stock_merch' => 'required|integer',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}