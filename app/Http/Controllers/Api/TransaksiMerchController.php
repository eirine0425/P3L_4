<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\TransaksiMerch\TransaksiMerchUseCase;
use App\DTOs\TransaksiMerch\CreateTransaksiMerchRequest;
use App\DTOs\TransaksiMerch\UpdateTransaksiMerchRequest;
use App\DTOs\TransaksiMerch\GetTransaksiMerchPaginationRequest;

class TransaksiMerchController extends Controller
{
    public function __construct(protected TransaksiMerchUseCase $transaksiMerchUseCase) {}

    public function index(GetTransaksiMerchPaginationRequest $request)
    {
        return response()->json($this->transaksiMerchUseCase->getAll($request));
    }

    public function store(CreateTransaksiMerchRequest $request)
    {
        $transaksiMerch = $this->transaksiMerchUseCase->create($request);
        return response()->json($transaksiMerch, 201);
    }

    public function show($id)
    {
        $transaksiMerch = $this->transaksiMerchUseCase->find($id);
        return $transaksiMerch
            ? response()->json($transaksiMerch)
            : response()->json(['message' => 'Transaksi Merch not found'], 404);
    }

    public function update(UpdateTransaksiMerchRequest $request, $id)
    {
        $transaksiMerch = $this->transaksiMerchUseCase->update($id, $request);

        return $transaksiMerch
            ? response()->json($transaksiMerch)
            : response()->json(['message' => 'Transaksi Merch not found'], 404);
    }

    public function destroy($id)
    {
        return $this->transaksiMerchUseCase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }
}
