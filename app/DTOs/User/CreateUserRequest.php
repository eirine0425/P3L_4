<?php

namespace App\DTOs\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'dob' => 'required|date',
            'phone_number' => 'nullable|string|max:20',
            'role_id' => 'required|integer|exists:roles,role_id', // Sesuaikan dengan nama tabel dan kolom role
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
