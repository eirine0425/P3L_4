<?php

namespace App\Repositories\Interfaces;

use App\Models\Organisasi;

interface OrganisasiRepositoryInterface
{
    public function getAll(): array;
    public function find(int $id): ?Organisasi;
    public function create(array $data): Organisasi;
    public function update(int $id, array $data): Organisasi;
    public function delete(int $id): bool;
}