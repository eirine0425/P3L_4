<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Pembeli\PembeliUseCase;
use App\DTOs\Pembeli\CreatePembeliRequest;
use App\DTOs\Pembeli\UpdatePembeliRequest;
use App\DTOs\Pembeli\GetPembeliPaginationRequest;

class PembeliController extends Controller
{
    public function __construct(
        protected PembeliUseCase $pembeliUseCase
    ) {}

    public function index(GetPembeliPaginationRequest $request)
    {
        return response()->json($this->pembeliUseCase->getAll($request));
    }
    public function store(CreatePembeliRequest $request)
    {
        $pembeli = $this->pembeliUseCase->create($request);
        return response()->json($pembeli, 201);
    }

    public function show($id)
    {
        $pembeli = $this->pembeliUseCase->find($id);
        return $pembeli ? response()->json($pembeli) : response()->json(['message' => 'Pembeli not found'], 404);
    }

    public function update(UpdatePembeliRequest $request, $id)
    {
        $pembeli = $this->pembeliUseCase->update($request, $id); // Perbaikan di sini
        return $pembeli ? response()->json($pembeli) : response()->json(['message' => 'Pembeli not found'], 404);
    }

    public function destroy($id)
    {
        return $this->pembeliUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
