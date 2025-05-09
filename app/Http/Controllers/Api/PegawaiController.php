<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Pegawai\PegawaiUseCase;
use App\DTOs\Pegawai\CreatePegawaiRequest;
use App\DTOs\Pegawai\GetPegawaiPaginationRequest;
use App\DTOs\Pegawai\UpdatePegawaiRequest;

class PegawaiController extends Controller
{
    public function __construct(protected PegawaiUseCase $pegawaiUseCase) {}

    public function index(GetPegawaiPaginationRequest $request)
    {
        return response()->json($this->pegawaiUseCase->getAll($request));
    }

    public function store(CreatePegawaiRequest $request)
    {
        $pegawai = $this->pegawaiUseCase->create($request);
        return response()->json($pegawai, 201);
    }

    public function show($id)
    {
        $pegawai = $this->pegawaiUseCase->find($id);
        return $pegawai
            ? response()->json($pegawai)
            : response()->json(['message' => 'Pegawai not found'], 404);
    }

    public function update(UpdatePegawaiRequest $request, $id)
    {
        $pegawai = $this->pegawaiUseCase->update($id, $request);

        return $pegawai
            ? response()->json($pegawai)
            : response()->json(['message' => 'Pegawai not found'], 404);
    }

    public function destroy($id)
    {
        return $this->pegawaiUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}