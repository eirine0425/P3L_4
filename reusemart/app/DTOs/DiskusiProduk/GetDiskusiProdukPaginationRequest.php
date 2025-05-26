<?php

namespace App\DTOs\DiskusiProduk;

use Illuminate\Foundation\Http\FormRequest;

class GetDiskusiProdukPaginationRequest extends FormRequest
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
        return (int) $this->input('page', 1);
    }

    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 10);
    }

    public function getSearch(): ?string
    {
        return $this->input('search', '');
    }
}