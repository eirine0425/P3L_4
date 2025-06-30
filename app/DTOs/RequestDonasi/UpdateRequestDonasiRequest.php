<?php

namespace App\DTOs\RequestDonasi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestDonasiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'request_id'         => 'sometimes|required|integer',
            'organisasi_id'      => 'sometimes|required|integer',
            'deskripsi'          => 'sometimes|required|string|max:255',
            'tanggal_donasi'    => 'sometimes|required|date',
            'status_request'     => 'sometimes|required|string|max:50',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
