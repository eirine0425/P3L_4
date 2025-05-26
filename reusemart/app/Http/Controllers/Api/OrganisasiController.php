<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Organisasi\OrganisasiUsecase;
use App\DTOs\Organisasi\CreateOrganisasiRequest;
use App\DTOs\Organisasi\UpdateOrganisasiRequest;
use App\Models\Organisasi;
use App\DTOs\Organisasi\GetOrganisasiPaginationRequest;

class OrganisasiController extends Controller
{
    public function __construct(
        protected OrganisasiUsecase $organisasiUsecase
    ) {}

    public function index(GetOrganisasiPaginationRequest $request)
    {
        return response()->json($this->organisasiUsecase->getAll($request));
    }

    public function store(CreateOrganisasiRequest $request)
    {
        $user = $this->organisasiUsecase->create($request);
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $organisasi = $this->organisasiUsecase->find($id);
        return $organisasi ? response()->json($organisasi) : response()->json(['message' => 'organisasi not found'], 404);
    }

    public function update(UpdateOrganisasiRequest $request, $id)
    {
        $organisasi = $this->organisasiUsecase->find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'organisasi not found'], 404);
        }

        $organisasi->update($request->validated());

        return response()->json($organisasi);
    }


    public function destroy($id)
    {
        return $this->organisasiUsecase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
