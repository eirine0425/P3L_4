<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Role\RoleUseCase;
use App\DTOs\Role\CreateRoleRequest;
use App\DTOs\Role\UpdateRoleRequest;

class RoleController extends Controller
{
    public function __construct(
        protected RoleUseCase $roleUseCase
    ) {}

    public function index()
    {
        return response()->json($this->roleUseCase->getAll());
    }

    public function store(CreateRoleRequest $request)
    {
        $role = $this->roleUseCase->create($request);
        return response()->json($role, 201);
    }

    public function show($id)
    {
        $role = $this->roleUseCase->find($id);
        return $role
            ? response()->json($role)
            : response()->json(['message' => 'Role not found'], 404);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->roleUseCase->update($request, $id);
        return $role
            ? response()->json($role)
            : response()->json(['message' => 'Role not found'], 404);
    }

    public function destroy($id)
    {
        return $this->roleUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}