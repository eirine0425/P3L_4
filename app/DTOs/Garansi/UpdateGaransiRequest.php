<?php

namespace App\DTOs\Garansi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGaransiRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tentukan apakah pengguna diizinkan untuk melakukan request ini
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|max:255',
            'tanggal_aktif' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_aktif',
        ];
    }
}
