<?php

namespace App\Repositories\Interfaces;


use App\Models\Merch;

interface MerchRepositoryInterface
{
    public function getAll(int $perPage = 10, int $page = 1, String $search = ""): array;
    public function find($id);
    public function create(array $data);
    public function update(Merch $merch, array $data);
    public function delete(Merch $merch);

}