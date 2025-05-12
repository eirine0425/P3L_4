<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Garansi\GaransiUseCase;
use App\DTOs\Garansi\CreateGaransiRequest;
use App\DTOs\Garansi\UpdateGaransiRequest;
use App\DTOs\Garansi\GetGaransiPaginationRequest;
use Illuminate\Http\Request;

class GaransiController extends Controller
{
    protected GaransiUseCase $garansiUseCase;

    public function __construct(GaransiUseCase $garansiUseCase)
    {
        $this->garansiUseCase = $garansiUseCase;
    }

    public function index(GetGaransiPaginationRequest $request)
    {
        return response()->json($this->garansiUseCase->getAll($request));
    }

    public function show($id)
    {
        $garansi = $this->garansiUseCase->find($id);

        if (!$garansi) {
            return response()->json(['message' => 'Garansi tidak ditemukan'], 404);
        }

        return response()->json($garansi);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'tanggal_aktif' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_aktif',
        ]);

        $createRequest = new CreateGaransiRequest($validated);
        $garansi = $this->garansiUseCase->create($createRequest);

        return response()->json($garansi, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'tanggal_aktif' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_aktif',
        ]);

        $updateRequest = new UpdateGaransiRequest($validated);
        $garansi = $this->garansiUseCase->update($id, $updateRequest);

        if (!$garansi) {
            return response()->json(['message' => 'Garansi tidak ditemukan'], 404);
        }

        return response()->json($garansi);
    }

    public function destroy($id)
    {
        $deleted = $this->garansiUseCase->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Garansi tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Garansi berhasil dihapus']);
    }
}
