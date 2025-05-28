<?php

namespace App\DTOs\TransaksiPenitipan;

use Illuminate\Foundation\Http\FormRequest;

class ExtendTransaksiPenitipanRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk request perpanjangan masa penitipan.
     */
    public function rules(): array
    {
        return [
            'transaksi_penitipan_id' => 'required|integer|exists:transaksi_penitipan,id',
        ];
    }

    /**
     * Pesan error kustom (opsional).
     */
    public function messages(): array
    {
        return [
            'transaksi_penitipan_id.required' => 'ID transaksi penitipan wajib diisi.',
            'transaksi_penitipan_id.integer' => 'ID transaksi harus berupa angka.',
            'transaksi_penitipan_id.exists' => 'Transaksi penitipan tidak ditemukan.',
        ];
    }
}
