<?php

namespace App\UseCases\Role;

use App\DTOs\Role\CreateRoleRequest;
use App\DTOs\Role\UpdateRoleRequest;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleUseCase
{
    public function __construct(
        protected RoleRepositoryInterface $repository
    ) {}

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function find($id)
    {
        return $this->repository->find($id); // Mencari role berdasarkan role_id
    }

    public function create(CreateRoleRequest $request)
    {
        $data = $request->only([
            'role_id',
            'nama_role',
        ]);
        return $this->repository->create($data);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->repository->find($id);
        if (!$role) {
            return null;
        }

        $data = $request->only([
            'role_id',
            'nama_role',
        ]);

        return $this->repository->update($id, $data); // Update berdasarkan role_id
    }

    public function delete($id): bool
    {
        $role = $this->repository->find($id);
        if (!$role) {
            return false;
        }

        return $this->repository->delete($id);
    }
}