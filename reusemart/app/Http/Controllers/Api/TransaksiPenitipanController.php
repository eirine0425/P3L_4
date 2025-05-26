<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\TransaksiPenitipan\TransaksiPenitipanUseCase;
use App\DTOs\TransaksiPenitipan\CreateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\UpdateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\GetTransaksiPenitipanPaginationRequest;

class TransaksiPenitipanController extends Controller
{
    public function __construct(protected TransaksiPenitipanUseCase $transaksiPenitipanUseCase) {}

    public function index(GetTransaksiPenitipanPaginationRequest $request)
    {
        return response()->json($this->transaksiPenitipanUseCase->getAll($request));
    }

    public function store(CreateTransaksiPenitipanRequest $request)
    {
        $transaksiPenitipan = $this->transaksiPenitipanUseCase->create($request);
        return response()->json($transaksiPenitipan, 201);
    }

    public function show($id)
    {
        $transaksiPenitipan = $this->transaksiPenitipanUseCase->find($id);
        return $transaksiPenitipan
            ? response()->json($transaksiPenitipan)
            : response()->json(['message' => 'Transaksi Penitipan not found'], 404);
    }

    public function update(UpdateTransaksiPenitipanRequest $request, $id)
    {
        $transaksiPenitipan = $this->transaksiPenitipanUseCase->update($id, $request);

        return $transaksiPenitipan
            ? response()->json($transaksiPenitipan)
            : response()->json(['message' => 'Transaksi Penitipan not found'], 404);
    }

    public function destroy($id)
    {
        return $this->transaksiPenitipanUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
