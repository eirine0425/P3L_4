<?php

namespace App\DTOs\Pegawai;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePegawaiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_jabatan' => 'sometimes|required|string|max:255',
            'tanggal_bergabung' => 'sometimes|required|date',
            'nominal_komisi' => 'sometimes|required|numeric|min:0',
            'status_aktif' => 'sometimes|required|string|in:aktif,non-aktif',
            'nama' => 'sometimes|required|string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}