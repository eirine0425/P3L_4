<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\KeranjangBelanja\KeranjangBelanjaUseCase;
use App\DTOs\KeranjangBelanja\CreateKeranjangBelanjaRequest;
use App\DTOs\KeranjangBelanja\UpdateKeranjangBelanjaRequest;
use App\DTOs\KeranjangBelanja\GetKeranjangBelanjaPaginationRequest;

class KeranjangBelanjaController extends Controller
{
    public function __construct(protected KeranjangBelanjaUseCase $keranjangBelanjaUseCase) {}

    public function index(GetKeranjangBelanjaPaginationRequest $request)
    {
        return response()->json($this->keranjangBelanjaUseCase->getAll($request));
    }

    public function store(CreateKeranjangBelanjaRequest $request)
    {
        $keranjangBelanja = $this->keranjangBelanjaUseCase->create($request);
        return response()->json($keranjangBelanja, 201);
    }

    public function show($id)
    {
        $keranjangBelanja = $this->keranjangBelanjaUseCase->find($id);
        return $keranjangBelanja
            ? response()->json($keranjangBelanja)
            : response()->json(['message' => 'Keranjang Belanja not found'], 404);
    }

    public function update(UpdateKeranjangBelanjaRequest $request, $id)
    {
        $keranjangBelanja = $this->keranjangBelanjaUseCase->update($id, $request);

        return $keranjangBelanja
            ? response()->json($keranjangBelanja)
            : response()->json(['message' => 'Keranjang Belanja not found'], 404);
    }

    public function destroy($id)
    {
        return $this->keranjangBelanjaUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
