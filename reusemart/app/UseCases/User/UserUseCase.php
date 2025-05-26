<?php

namespace App\UseCases\User;

use App\DTOs\User\CreateUserRequest;
use App\DTOs\User\GetUserPaginationRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserUseCase
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    public function getAll(GetUserPaginationRequest $request): array
    {
        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            filters: [] // Add appropriate filters or an empty array as needed
        );
    }

    public function create(CreateUserRequest $request)
    {
        $data = $request->only(['username', 'password', 'email', 'no_hp', 'role_id']);
        $data['password'] = bcrypt($data['password']);
        return $this->repository->create($data);
    }
}
