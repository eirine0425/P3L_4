<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\TransaksiPenitipan\TransaksiPenitipanUseCase;
use App\DTOs\TransaksiPenitipan\ExtendTransaksiPenitipanRequest;
use Illuminate\Http\Request;

class PenitipTransaksiController extends Controller
{
    public function __construct(protected TransaksiPenitipanUseCase $transaksiPenitipanUseCase) {}

    /**
     * Get transaksi penitipan for authenticated penitip
     */
    public function myTransaksi(Request $request)
    {
        // Assuming penitip_id is available from authenticated user
        $penitipId = auth()->user()->penitip_id ?? null;
        
        if (!$penitipId) {
            return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
        }

        $transaksi = $this->transaksiPenitipanUseCase->getByPenitipId($penitipId);
        return response()->json($transaksi);
    }

    /**
     * Extend penitipan duration by penitip
     */
    public function extendMyPenitipan(ExtendTransaksiPenitipanRequest $request)
    {
        $penitipId = auth()->user()->penitip_id ?? null;
        
        if (!$penitipId) {
            return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
        }

        $transaksiPenitipanId = $request->input('transaksi_penitipan_id');
        
        // Verify that this transaksi belongs to the authenticated penitip
        $transaksi = $this->transaksiPenitipanUseCase->find($transaksiPenitipanId);
        
        if (!$transaksi || $transaksi->penitip_id != $penitipId) {
            return response()->json(['message' => 'Transaksi penitipan tidak ditemukan atau bukan milik Anda'], 404);
        }

        // Check if extension is already used
        if ($transaksi->status_perpanjangan == true) {
            return response()->json([
                'message' => 'Masa penitipan sudah pernah diperpanjang sebelumnya. Setiap transaksi hanya dapat diperpanjang sekali.'
            ], 400);
        }

        $result = $this->transaksiPenitipanUseCase->extendPenitipan($transaksiPenitipanId);

        if (!$result) {
            return response()->json([
                'message' => 'Gagal memperpanjang masa penitipan.'
            ], 400);
        }

        return response()->json([
            'message' => 'Masa penitipan berhasil diperpanjang +30 hari',
            'data' => $result,
            'new_expiry_date' => $result->batas_penitipan,
            'status_perpanjangan' => $result->status_perpanjangan
        ], 200);
    }
}
