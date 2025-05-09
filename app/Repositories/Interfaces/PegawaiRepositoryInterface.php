<?php

namespace App\Repositories\Interfaces;

interface PegawaiRepositoryInterface
{
    public function getAll(int $perPage = 10, string $search ="", int $page = 1): array;
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}