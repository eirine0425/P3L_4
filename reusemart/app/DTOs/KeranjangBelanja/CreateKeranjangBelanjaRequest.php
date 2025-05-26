<?php

namespace App\DTOs\KeranjangBelanja;

use Illuminate\Foundation\Http\FormRequest;

class CreateKeranjangBelanjaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'barang_id' => 'required|integer|exists:barang,barang_id',
            'pembeli_id' => 'required|integer|exists:pembeli,pembeli_id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}