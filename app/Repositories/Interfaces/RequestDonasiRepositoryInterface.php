<?php

namespace App\Repositories\Interfaces;

use App\Models\RequestDonasi;
use App\DTOs\RequestDonasi\GetRequestDonasiPaginationRequest;

interface RequestDonasiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search ="", int $page = 1): array;
    public function find(int $id): ?RequestDonasi;
    public function create(array $data): RequestDonasi;
    public function update(int $id, array $data): RequestDonasi;
    public function delete(int $id): bool;
}
