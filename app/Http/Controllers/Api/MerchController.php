<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Merch\MerchUseCase;
use App\DTOs\Merch\CreateMerchRequest;
use App\DTOs\Merch\UpdateMerchRequest;
use Illuminate\Http\Request;
use App\DTOs\Merch\GetMerchPaginationRequest;


class MerchController extends Controller
{
    public function __construct(protected MerchUseCase $merchUseCase) {}

    // Get all merch
    public function index(GetMerchPaginationRequest $request)
    {
        return response()->json($this->merchUseCase->getAll($request));
    }

    // Create a new merch
    public function store(CreateMerchRequest $request)
    {
        $merch = $this->merchUseCase->create($request);
        return response()->json($merch, 201); // Return the created merch
    }

    // Show a single merch by ID
    public function show($id)
    {
        $merch = $this->merchUseCase->find($id);
        return $merch
            ? response()->json($merch)
            : response()->json(['message' => 'Merch not found'], 404);
    }

    // Update a merch by ID
    public function update(UpdateMerchRequest $request, $id)
    {
        $merch = $this->merchUseCase->update($id, $request);

        return $merch
            ? response()->json($merch)
            : response()->json(['message' => 'Merch not found'], 404);
    }

    // Delete a merch by ID
    public function destroy($id)
    {
        return $this->merchUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}