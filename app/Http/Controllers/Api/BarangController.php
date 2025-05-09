<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Barang\BarangUseCase;
use App\DTOs\Barang\CreateBarangRequest;
use App\DTOs\Barang\UpdateBarangRequest;

class BarangController extends Controller
{
    public function __construct(protected BarangUseCase $barangUseCase) {}

    public function index()
    {
        return response()->json($this->barangUseCase->getAll());
    }

    public function store(CreateBarangRequest $request)
    {
        $barang = $this->barangUseCase->create($request);
        return response()->json($barang, 201);
    }

    public function show($id)
    {
        $barang = $this->barangUseCase->find($id);
        return $barang
            ? response()->json($barang)
            : response()->json(['message' => 'Barang not found'], 404);
    }

    public function update(UpdateBarangRequest $request, $id)
    {
        $barang = $this->barangUseCase->update($id, $request);

        return $barang
            ? response()->json($barang)
            : response()->json(['message' => 'Barang not found'], 404);
    }

    public function destroy($id)
    {
        return $this->barangUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}