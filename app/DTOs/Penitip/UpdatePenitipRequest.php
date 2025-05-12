<?php

namespace App\DTOs\Penitip;


use Illuminate\Foundation\Http\FormRequest;

class UpdatePenitipRequest extends FormRequest
{
    public function rules(): array
{
    return [
        'nama'               => 'sometimes|required|string|max:100',
        'point_reward'       => 'sometimes|required|integer|min:0',
        'tanggal_registrasi' => 'sometimes|required|date',
        'no_ktp'             => 'sometimes|required|string|max:20',
        'badge'              => 'sometimes|nullable|string|max:50',
        'periode'            => 'sometimes|nullable|string|max:50',
    ];
}


    public function authorize(): bool
    {
        return true;
    }
}