<?php

namespace App\DTOs\Barang;

use Illuminate\Foundation\Http\FormRequest;

class GetBarangPaginationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'status' => 'sometimes|string|in:semua_status,menunggu_verifikasi,aktif,tidak_aktif,terjual,ditolak',
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
        return $this->input('search');
    }

    public function getStatus(): ?string
    {
        $status = $this->input('status');
        return ($status === 'semua_status' || empty($status)) ? null : $status;
    }

    public function getFilters(): array
    {
        return [
            'search' => $this->getSearch(),
            'status' => $this->getStatus(),
        ];
    }
}
