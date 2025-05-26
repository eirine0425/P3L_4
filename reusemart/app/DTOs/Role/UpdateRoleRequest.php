<?php

namespace App\DTOs\Role;

use App\DTOs\BaseDto;
use Illuminate\Http\Request;

class UpdateRoleRequest extends BaseDto
{
    public function rules(): array
    {
        return [
            'role_id'   => 'sometimes|required|integer',
            'nama_role' => 'sometimes|required|string|max:100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}