<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Pengiriman\PengirimanUseCase;
use App\DTOs\Pengiriman\CreatePengirimanRequest;
use App\DTOs\Pengiriman\UpdatePengirimanRequest;
use App\DTOs\Pengiriman\GetPengirimanPaginationRequest;

class PengirimanController extends Controller
{
    public function __construct(protected PengirimanUseCase $pengirimanUseCase) {}

    public function index(GetPengirimanPaginationRequest $request)
    {
        return response()->json($this->pengirimanUseCase->getAll($request));
    }

    public function store(CreatePengirimanRequest $request)
    {
        $pengiriman = $this->pengirimanUseCase->create($request);
        return response()->json($pengiriman, 201);
    }

    public function show($id)
    {
        $pengiriman = $this->pengirimanUseCase->find($id);
        return $pengiriman
            ? response()->json($pengiriman)
            : response()->json(['message' => 'Pengiriman not found'], 404);
    }

    public function update(UpdatePengirimanRequest $request, $id)
    {
        $pengiriman = $this->pengirimanUseCase->update($id, $request);

        return $pengiriman
            ? response()->json($pengiriman)
            : response()->json(['message' => 'Pengiriman not found'], 404);
    }

    public function destroy($id)
    {
        return $this->pengirimanUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
