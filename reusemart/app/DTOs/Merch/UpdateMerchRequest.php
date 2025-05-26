<?php
namespace App\DTOs\Merch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMerchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama' => 'sometimes|required|string|max:255',
            'jumlah_poin' => 'sometimes|required|integer',
            'stock_merch' => 'sometimes|required|integer',
        ];
    }

    public function authorize(): bool
    {
        return true; // atau bisa validasi berdasarkan user
    }
}