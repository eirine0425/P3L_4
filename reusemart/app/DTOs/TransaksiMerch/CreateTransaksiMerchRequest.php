<?php

namespace App\DTOs\TransaksiMerch;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransaksiMerchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pembeli_id' => 'required|integer|exists:pembeli,pembeli_id',
            'merch_id' => 'required|integer|exists:merch,merch_id',
            'tanggal_penukaran' => 'required|date',
            'status' => 'required|string|max:50',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}