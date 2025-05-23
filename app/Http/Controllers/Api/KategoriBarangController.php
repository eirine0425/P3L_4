<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\KategoriBarang\KategoriBarangUseCase;
use App\DTOs\KategoriBarang\CreateKategoriBarangRequest;
use App\DTOs\KategoriBarang\UpdateKategoriBarangRequest;
use App\DTOs\KategoriBarang\GetKategoriBarangPaginationRequest;

class KategoriBarangController extends Controller
{
    public function __construct(
        protected KategoriBarangUseCase $kategoriBarangUseCase
    ) {}

    // Fetch all kategori barang
    public function index(GetKategoriBarangPaginationRequest $request)
    {
        return response()->json($this->kategoriBarangUseCase->getAll($request));
    }

    // Create a new kategori barang
    public function store(CreateKategoriBarangRequest $request)
    {
        $kategoriBarang = $this->kategoriBarangUseCase->create($request);
        return response()->json($kategoriBarang, 201);
    }

    // Show a specific kategori barang by id
    public function show($id)
    {
        $kategoriBarang = $this->kategoriBarangUseCase->find($id);
        return $kategoriBarang
            ? response()->json($kategoriBarang)
            : response()->json(['message' => 'Kategori Barang not found'], 404);
    }

    // Update a specific kategori barang by id
    public function update(UpdateKategoriBarangRequest $request, $id)
    {
        $kategoriBarang = $this->kategoriBarangUseCase->update($request, $id);
        return $kategoriBarang
            ? response()->json($kategoriBarang)
            : response()->json(['message' => 'Kategori Barang not found'], 404);
    }

    // Delete a specific kategori barang by id
    public function destroy($id)
    {
        return $this->kategoriBarangUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Kategori Barang not found'], 404);
    }
}
