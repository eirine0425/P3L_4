<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Alamat\AlamatUsecase;
use App\DTOs\Alamat\CreateAlamatRequest;
use App\DTOs\Alamat\UpdateALamatRequest;
use App\Models\Alamat;
use App\DTOs\Alamat\GetAlamatPaginationRequest;

class AlamatController extends Controller
{
    public function __construct(
        protected AlamatUsecase $alamatUsecase
    ) {}

    public function index(GetAlamatPaginationRequest $request)
    {
        return response()->json($this->alamatUsecase->getAll($request));
    }

    public function store(CreateAlamatRequest $request)
    {
        $user = $this->alamatUsecase->create($request);
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $alamat = $this->alamatUsecase->find($id);
        return $alamat ? response()->json($alamat) : response()->json(['message' => 'alamat not found'], 404);
    }

    public function update(UpdateAlamatRequest $request, $id)
    {
        $alamat = $this->alamatUsecase->find($id);
        if (!$alamat) {
            return response()->json(['message' => 'Alamat not found'], 404);
        }

        $alamat->update($request->validated());

        return response()->json($alamat);
    }


    public function destroy($id)
    {
        return $this->alamatUsecase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
