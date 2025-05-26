<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Penitip\PenitipUseCase;
use App\DTOs\Penitip\CreatePenitipRequest;
use App\DTOs\Penitip\UpdatePenitipRequest;
use App\DTOs\Penitip\GetPenitipPaginationRequest;
use App\Models\Penitip;

class PenitipController extends Controller
{
    public function __construct(
        protected PenitipUseCase $PenitipUseCase
    ) {}

    public function index(GetPenitipPaginationRequest $request)
    {
        $penitips = $this->PenitipUseCase->getAll($request);
        return response()->json([
            'data' => $penitips,
            'meta' => [
                'total' => count($penitips),
                'page' => $request->getPage(),
                'per_page' => $request->getPerPage()
            ]
        ]);
    }

    public function store(CreatePenitipRequest $request)
    {
        $penitip = $this->PenitipUseCase->create($request);
        return response()->json($penitip, 201);
    }

    public function show($id)
    {
        $penitip = $this->PenitipUseCase->find($id);
        return $penitip 
            ? response()->json(['data' => $penitip]) 
            : response()->json(['message' => 'Penitip not found'], 404);
    }

    public function update(UpdatePenitipRequest $request, $id)
    {
        try {
            $penitip = $this->PenitipUseCase->update($request, $id);
            return $penitip 
                ? response()->json(['message' => 'Penitip updated successfully!', 'data' => $penitip])
                : response()->json(['message' => 'Penitip not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update penitip', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            return $this->PenitipUseCase->delete($id)
                ? response()->json(['message' => 'Penitip deleted successfully'])
                : response()->json(['message' => 'Penitip not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete penitip', 'error' => $e->getMessage()], 500);
        }
    }
}
