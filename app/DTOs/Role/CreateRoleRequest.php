<?php

namespace App\DTOs\Role;

use App\DTOs\BaseDto;
use Illuminate\Http\Request;

class CreateRoleRequest extends BaseDto
{
    public int $role_id;
    public string $nama_role;

    public function __construct(Request $request)
    {
        $this->role_id = $request->input('role_id');
        $this->nama_role = $request->input('nama_role');
    }

    public function toArray(): array
    {
        return [
            'role_id'   => $this->role_id,
            'nama_role' => $this->nama_role,
        ];
    }
}