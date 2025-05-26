<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Transaksi\TransaksiUseCase;
use App\DTOs\Transaksi\CreateTransaksiRequest;
use App\DTOs\Transaksi\UpdateTransaksiRequest;
use App\DTOs\Transaksi\GetTransaksiPaginationRequest;

class TransaksiController extends Controller
{
    public function __construct(protected TransaksiUseCase $transaksiUseCase) {}

    public function index(GetTransaksiPaginationRequest $request)
    {
        return response()->json($this->transaksiUseCase->getAll($request));
    }

    public function store(CreateTransaksiRequest $request)
    {
        $transaksi = $this->transaksiUseCase->create($request);
        return response()->json($transaksi, 201);
    }

    public function show($id)
    {
        $transaksi = $this->transaksiUseCase->find($id);
        return $transaksi
            ? response()->json($transaksi)
            : response()->json(['message' => 'Transaksi not found'], 404);
    }

    public function update(UpdateTransaksiRequest $request, $id)
    {
        $transaksi = $this->transaksiUseCase->update($id, $request);

        return $transaksi
            ? response()->json($transaksi)
            : response()->json(['message' => 'Transaksi not found'], 404);
    }

    public function destroy($id)
    {
        return $this->transaksiUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
