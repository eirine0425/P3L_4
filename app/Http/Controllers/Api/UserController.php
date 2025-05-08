<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\DTOs\User\CreateUserRequest;
use App\DTOs\User\UpdateUserRequest;
use App\UseCases\User\UpdateUserUseCase;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\UseCases\User\UserUseCase;
use App\DTOs\User\GetUserPaginationRequest;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected UserUseCase $createUser,
        // protected UpdateUserUseCase $updateUser,
    ) {}

    public function index(GetUserPaginationRequest $request, UserUseCase $useCase)
    {
        return response()->json([
            'data' => $useCase->getAll($request),
        ]);
    }

    public function store(CreateUserRequest $request)
    {

        $user = $this->createUser->create($request);
        return response()->json($user, 201);
    }
    // public function store(CreateUserRequest $request)    
    public function show($id)
    {
        $user = $this->userRepo->find($id);
        return $user ? response()->json($user) : response()->json(['message' => 'User not found'], 404);
    }

    // public function update(UpdateUserRequest $request, $id)
    // {
    //     $user = $this->updateUser->execute($id, $request);
    //     return response()->json($user);
    // }

    public function destroy($id)
    {
        return $this->userRepo->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
