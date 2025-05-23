<?php

namespace App\DTOs\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required_with:password|same:password',
            'dob' => 'required|date',
            'phone_number' => 'nullable|string|max:20',
        ];
        
        // Hanya izinkan role tertentu untuk register mandiri
        $allowedRoles = [4, 7]; // pembeli, organisasi
        $allowedPegawaiRoles = [1, 3, 6, 8, 9, 10]; // admin, cs, kurir, owner, hunter, gudang
        
        if (request()->has('role_id')) {
            $rules['role_id'] = 'required|integer|in:' . implode(',', $allowedRoles);
        } else if (request()->has('role')) {
            $rules['role'] = 'required|in:pembeli,pegawai,organisasi';
        }
        
        // Role-specific validations
        if (request('role') == 'pegawai') {
            $rules['jabatan_role_id'] = 'required|integer|in:' . implode(',', $allowedPegawaiRoles);
            $rules['alamat'] = 'required|string';
            $rules['gaji_harapan'] = 'nullable|numeric|min:0';
            $rules['pengalaman'] = 'nullable|string';
        }
        
        if (request('role') == 'organisasi' || request('role_id') == 7) {
            $rules['address'] = 'required|string';
            $rules['description'] = 'required|string';
            $rules['document'] = 'required|file|mimes:pdf|max:2048';
        }
        
        return $rules;
    }

    public function messages(): array
    {
        return [
            'role_id.in' => 'Role yang dipilih tidak diizinkan untuk registrasi mandiri.',
            'jabatan_role_id.required' => 'Jabatan yang dilamar wajib dipilih.',
            'jabatan_role_id.in' => 'Jabatan yang dipilih tidak valid.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'document.required' => 'Dokumen legalitas organisasi wajib diupload.',
            'document.mimes' => 'Dokumen harus berformat PDF.',
            'document.max' => 'Ukuran dokumen maksimal 2MB.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
