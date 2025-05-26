<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\RequestDonasi\RequestDonasiUseCase;
use App\DTOs\RequestDonasi\CreateRequestDonasiRequest;
use App\DTOs\RequestDonasi\UpdateRequestDonasiRequest;
use App\DTOs\RequestDonasi\GetRequestDonasiPaginationRequest;

class RequestDonasiController extends Controller
{
    public function __construct(
        protected RequestDonasiUseCase $requestDonasiUseCase
    ) {}

    public function index(GetRequestDonasiPaginationRequest $request)
    {
        return response()->json($this->requestDonasiUseCase->getAll($request));
    }

    public function store(CreateRequestDonasiRequest $request)
    {
        $requestDonasi = $this->requestDonasiUseCase->create($request);
        return response()->json($requestDonasi, 201);
    }

    public function show($id)
    {
        $requestDonasi = $this->requestDonasiUseCase->find($id);
        return $requestDonasi ? response()->json($requestDonasi) : response()->json(['message' => 'Request Donasi not found'], 404);
    }

    public function update(UpdateRequestDonasiRequest $request, $id)
    {
        $requestDonasi = $this->requestDonasiUseCase->update($request, $id); // Passing request and id
        return $requestDonasi ? response()->json($requestDonasi) : response()->json(['message' => 'Request Donasi not found'], 404);
    }

    public function destroy($id)
    {
        return $this->requestDonasiUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
