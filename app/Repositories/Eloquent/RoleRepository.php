<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function getAll(): array
    {
        return Role::all()->toArray();
    }

    public function find(int $id): ?Role
    {
        return Role::where('role_id', $id)->first(); // Using request_id
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->find($id);
        if ($role) {
            $role->update($data);
        }
        return $role;
    }

    public function delete(int $id): bool
    {
        $role = $this->find($id);
        if ($role) {
            return $role->delete();
        }
        return false;
    }
}