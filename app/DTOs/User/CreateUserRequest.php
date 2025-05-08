<?php

namespace App\DTOs\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'email' => 'required|email|max:100',
            'no_hp' => 'nullable|string|max:20',
            'role_id' => 'required|integer|exists:role,role_id'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
