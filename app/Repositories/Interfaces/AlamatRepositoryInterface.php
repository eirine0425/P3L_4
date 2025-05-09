<?php

namespace App\Repositories\Interfaces;

use App\Models\Alamat;

interface AlamatRepositoryInterface
{
    public function getAll(): array;
    public function find(int $id): ?Alamat;
    public function create(array $data): Alamat;
    public function update(int $id, array $data): Alamat;
    public function delete(int $id): bool;
}