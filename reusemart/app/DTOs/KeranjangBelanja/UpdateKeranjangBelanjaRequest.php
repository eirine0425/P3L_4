<?php

namespace App\DTOs\KeranjangBelanja;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKeranjangBelanjaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'barang_id' => 'sometimes|integer|exists:barang,barang_id',
            'pembeli_id' => 'sometimes|integer|exists:pembeli,pembeli_id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}