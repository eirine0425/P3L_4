<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\DiskusiProduk\DiskusiProdukUseCase;
use App\DTOs\DiskusiProduk\CreateDiskusiProdukRequest;
use App\DTOs\DiskusiProduk\UpdateDiskusiProdukRequest;
use App\DTOs\DiskusiProduk\GetDiskusiProdukPaginationRequest;

class DiskusiProdukController extends Controller
{
    public function __construct(protected DiskusiProdukUseCase $diskusiProdukUseCase) {}

    public function index(GetDiskusiProdukPaginationRequest $request)
    {
        return response()->json($this->diskusiProdukUseCase->getAll($request));
    }

    public function store(CreateDiskusiProdukRequest $request)
    {
        $diskusiProduk = $this->diskusiProdukUseCase->create($request);
        return response()->json($diskusiProduk, 201);
    }

    public function show($id)
    {
        $diskusiProduk = $this->diskusiProdukUseCase->find($id);
        return $diskusiProduk
            ? response()->json($diskusiProduk)
            : response()->json(['message' => 'Diskusi Produk not found'], 404);
    }

    public function update(UpdateDiskusiProdukRequest $request, $id)
    {
        $diskusiProduk = $this->diskusiProdukUseCase->update($id, $request);

        return $diskusiProduk
            ? response()->json($diskusiProduk)
            : response()->json(['message' => 'Diskusi Produk not found'], 404);
    }

    public function destroy($id)
    {
        return $this->diskusiProdukUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}