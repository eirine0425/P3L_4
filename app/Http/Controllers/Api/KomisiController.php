<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Komisi\KomisiUseCase;
use App\DTOs\Komisi\CreateKomisiRequest;
use App\DTOs\Komisi\UpdateKomisiRequest;
use App\DTOs\Komisi\GetKomisiPaginationRequest;

class KomisiController extends Controller
{
    public function __construct(protected KomisiUseCase $komisiUseCase) {}

    public function index(GetKomisiPaginationRequest $request)
    {
        return response()->json($this->komisiUseCase->getAll($request));
    }

    public function store(CreateKomisiRequest $request)
    {
        $komisi = $this->komisiUseCase->create($request);
        return response()->json($komisi, 201);
    }

    public function show($id)
    {
        $komisi = $this->komisiUseCase->find($id);
        return $komisi
            ? response()->json($komisi)
            : response()->json(['message' => 'Komisi not found'], 404);
    }

    public function update(UpdateKomisiRequest $request, $id)
    {
        $komisi = $this->komisiUseCase->update($id, $request);

        return $komisi
            ? response()->json($komisi)
            : response()->json(['message' => 'Komisi not found'], 404);
    }

    public function destroy($id)
    {
        return $this->komisiUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}