<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransaksiPenitipanRequest;
use App\Http\Requests\ExtendTransaksiPenitipanRequest;
use App\Http\Resources\TransaksiPenitipanResource;
use App\UseCases\TransaksiPenitipanUseCase;
use Illuminate\Http\Request;

class TransaksiPenitipanController extends Controller
{
    private $transaksiPenitipanUseCase;

    public function __construct(TransaksiPenitipanUseCase $transaksiPenitipanUseCase)
    {
        $this->transaksiPenitipanUseCase = $transaksiPenitipanUseCase;
    }

    public function index()
    {
        $transaksiPenitipans = $this->transaksiPenitipanUseCase->getAllTransaksiPenitipan();
        return TransaksiPenitipanResource::collection($transaksiPenitipans);
    }

    public function store(TransaksiPenitipanRequest $request)
    {
        $data = $request->validated();
        $transaksiPenitipan = $this->transaksiPenitipanUseCase->createTransaksiPenitipan($data);

        return new TransaksiPenitipanResource($transaksiPenitipan);
    }

    public function show($id)
    {
        $transaksiPenitipan = $this->transaksiPenitipanUseCase->getTransaksiPenitipanById($id);

        if (!$transaksiPenitipan) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi Penitipan not found'
            ], 404);
        }

        return new TransaksiPenitipanResource($transaksiPenitipan);
    }

    public function update(TransaksiPenitipanRequest $request, $id)
    {
        $data = $request->validated();
        $transaksiPenitipan = $this->transaksiPenitipanUseCase->updateTransaksiPenitipan($id, $data);

        if (!$transaksiPenitipan) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi Penitipan not found'
            ], 404);
        }

        return new TransaksiPenitipanResource($transaksiPenitipan);
    }

    public function destroy($id)
    {
        $result = $this->transaksiPenitipanUseCase->deleteTransaksiPenitipan($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi Penitipan not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi Penitipan deleted successfully'
        ]);
    }

    public function extendPenitipan($id)
    {
        $result = $this->transaksiPenitipanUseCase->extendPenitipan($id);
        
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Perpanjangan tidak dapat dilakukan. Transaksi tidak ditemukan atau sudah pernah diperpanjang sebelumnya.'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Masa penitipan berhasil diperpanjang',
            'data' => $result
        ]);
    }
}