<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Donasi\DonasiUseCase;
use App\DTOs\Donasi\CreateDonasiRequest;
use App\DTOs\Donasi\UpdateDonasiRequest;
use App\DTOs\Donasi\GetDonasiPaginationRequest;



use Illuminate\Http\Request;

class DonasiController extends Controller
{
    public function __construct(protected DonasiUseCase $donasiUseCase) {}

    // Get all donations
    public function index(GetDonasiPaginationRequest $request)
    {
        return response()->json($this->donasiUseCase->getAll($request));
    }

    // Create a new donation
    public function store(CreateDonasiRequest $request)
    {
        $donasi = $this->donasiUseCase->create($request);
        return response()->json($donasi, 201); // Return the created donation
    }

    // Show a single donation by ID
    public function show($id)
    {
        $donasi = $this->donasiUseCase->find($id);
        return $donasi
            ? response()->json($donasi)
            : response()->json(['message' => 'Donasi not found'], 404);
    }

    // Update a donation by ID
    public function update(UpdateDonasiRequest $request, $id)
    {
        $donasi = $this->donasiUseCase->update($id, $request);

        return $donasi
            ? response()->json($donasi)
            : response()->json(['message' => 'Donasi not found'], 404);
    }

    // Delete a donation by ID
    public function destroy($id)
    {
        return $this->donasiUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
