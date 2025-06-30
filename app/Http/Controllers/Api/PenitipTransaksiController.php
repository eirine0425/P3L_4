<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\TransaksiPenitipan\TransaksiPenitipanUseCase;
use App\DTOs\TransaksiPenitipan\ExtendTransaksiPenitipanRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenitipTransaksiController extends Controller
{
    public function __construct(protected TransaksiPenitipanUseCase $transaksiPenitipanUseCase) {}

    /**
     * Menampilkan semua transaksi penitipan milik penitip yang sedang login.
     */
    public function myTransaksi(Request $request)
    {
        $penitipId = auth()->user()->penitip_id ?? null;

        if (!$penitipId) {
            return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
        }

        $transaksi = $this->transaksiPenitipanUseCase->getByPenitipId($penitipId);
        return response()->json($transaksi);
    }

    /**
     * Memperpanjang masa penitipan sebanyak 30 hari, hanya sekali per transaksi.
     */
     public function extendMyPenitipan(ExtendTransaksiPenitipanRequest $request)
    {
        $penitipId = auth()->user()->penitip_id ?? null;

        if (!$penitipId) {
            return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
        }

        $transaksiPenitipanId = $request->input('transaksi_penitipan_id');

        $transaksi = $this->transaksiPenitipanUseCase->find($transaksiPenitipanId);

        // Pastikan transaksi valid dan milik penitip tersebut
        if (!$transaksi || $transaksi->penitip_id != $penitipId) {
            return response()->json(['message' => 'Transaksi penitipan tidak ditemukan atau bukan milik Anda'], 404);
        }

        // Cek apakah sudah diperpanjang sebelumnya - INI YANG DIPERBAIKI
        if ($transaksi->status_perpanjangan) {
            return response()->json(['message' => 'Masa penitipan hanya bisa diperpanjang satu kali.'], 400);
        }

        // Panggil use case untuk melakukan perpanjangan
        $result = $this->transaksiPenitipanUseCase->extendPenitipan($transaksiPenitipanId);

        if (!$result) {
            return response()->json([
                'message' => 'Gagal memperpanjang masa penitipan.'
            ], 500);
        }

        return response()->json([
            'message' => 'Masa penitipan berhasil diperpanjang selama 30 hari.',
            'data' => $result,
            'new_expiry_date' => $result->batas_penitipan,
            'status_perpanjangan' => $result->status_perpanjangan
        ], 200);
    }
}
