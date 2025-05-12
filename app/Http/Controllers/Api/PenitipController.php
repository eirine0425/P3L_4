<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Penitip\PenitipUseCase;
use App\DTOs\Penitip\CreatePenitipRequest;
use App\DTOs\Penitip\UpdatePenitipRequest;

class PenitipController extends Controller
{
    public function __construct(
        protected PenitipUseCase $PenitipUseCase
    ) {}

    public function index()
    {
        return response()->json($this->PenitipUseCase->getAll());
    }

    public function store(CreatePenitipRequest $request)
    {
        $penitip = $this->PenitipUseCase->create($request);
        return response()->json($penitip, 201);
    }

    public function show($id)
    {
        $penitip = $this->PenitipUseCase->find($id);
        return $penitip ? response()->json($penitip) : response()->json(['message' => 'Penitip not found'], 404);
    }

    public function update(UpdatePenitipRequest $request, $id)
    {
        $penitip = $this->PenitipUseCase->update($request, $id); // Passing request and id
        return $penitip ? response()->json($penitip) : response()->json(['message' => 'Penitip not found'], 404);
    }

    public function destroy($id)
    {
        return $this->PenitipUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}