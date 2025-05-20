<?php

namespace App\DTOs\TransaksiMerch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransaksiMerchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'sometimes|string|max:50',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}