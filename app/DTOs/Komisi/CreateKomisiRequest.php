<?php

namespace App\DTOs\Komisi;

use Illuminate\Foundation\Http\FormRequest;

class CreateKomisiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|integer|exists:pegawai,pegawai_id',
            'penitip_id' => 'required|integer|exists:penitip,penitip_id',
            'barang_id' => 'required|integer|exists:barang,barang_id',
            'persentase' => 'required|numeric|min:0|max:100',
            'nominal_komisi' => 'required|numeric|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}