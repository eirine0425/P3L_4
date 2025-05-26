<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\DetailTransaksi\DetailTransaksiUseCase;
use App\DTOs\DetailTransaksi\CreateDetailTransaksiRequest;
use App\DTOs\DetailTransaksi\UpdateDetailTransaksiRequest;
use App\DTOs\DetailTransaksi\GetDetailTransaksiPaginationRequest;
use Illuminate\Http\Request;

class DetailTransaksiController extends Controller
{
    public function __construct(protected DetailTransaksiUseCase $detailTransaksiUseCase) {}

    public function index(GetDetailTransaksiPaginationRequest $request)
    {
        return response()->json($this->detailTransaksiUseCase->getAll($request));
    }

    public function store(CreateDetailTransaksiRequest $request)
    {
        $detailTransaksi = $this->detailTransaksiUseCase->create($request);
        return response()->json($detailTransaksi, 201);
    }

    public function show(Request $request)
    {
        $barangId = $request->input('barang_id');
        $transaksiId = $request->input('transaksi_id');
        
        if (!$barangId || !$transaksiId) {
            return response()->json(['message' => 'Both barang_id and transaksi_id are required'], 400);
        }
        
        $detailTransaksi = $this->detailTransaksiUseCase->find($barangId, $transaksiId);
        return $detailTransaksi
            ? response()->json($detailTransaksi)
            : response()->json(['message' => 'Detail Transaksi not found'], 404);
    }

    public function update(UpdateDetailTransaksiRequest $request)
    {
        $barangId = $request->input('barang_id');
        $transaksiId = $request->input('transaksi_id');
        
        if (!$barangId || !$transaksiId) {
            return response()->json(['message' => 'Both barang_id and transaksi_id are required'], 400);
        }
        
        $detailTransaksi = $this->detailTransaksiUseCase->update($barangId, $transaksiId, $request);

        return $detailTransaksi
            ? response()->json($detailTransaksi)
            : response()->json(['message' => 'Detail Transaksi not found'], 404);
    }

    public function destroy(Request $request)
    {
        $barangId = $request->input('barang_id');
        $transaksiId = $request->input('transaksi_id');
        
        if (!$barangId || !$transaksiId) {
            return response()->json(['message' => 'Both barang_id and transaksi_id are required'], 400);
        }
        
        return $this->detailTransaksiUseCase->delete($barangId, $transaksiId)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
