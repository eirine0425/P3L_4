<?php

namespace App\DTOs\Penitip;

use Illuminate\Foundation\Http\FormRequest;

class GetPenitipPaginationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getPage(): int
    {
        return (int) $this->input('page', 1); // default ke halaman 1 jika tidak ada input
    }

    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 10); // default ke 10 item per halaman
    }

    public function getSearch(): ?string
    {
        return $this->input('search', ''); // default ke string kosong jika tidak ada search term
    }
}
