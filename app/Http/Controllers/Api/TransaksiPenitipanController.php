<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\TransaksiPenitipan\TransaksiPenitipanUseCase;
use App\DTOs\TransaksiPenitipan\CreateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\UpdateTransaksiPenitipanRequest;
use App\DTOs\TransaksiPenitipan\GetTransaksiPenitipanPaginationRequest;
use Illuminate\Http\Request;

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

    public function expiring(Request $request)
    {
        $days = $request->get('days', 7);
        $expiring = $this->transaksiPenitipanUseCase->getExpiringConsignments($days);
        return response()->json($expiring);
    }

    public function expired()
    {
        $expired = $this->transaksiPenitipanUseCase->getExpiredConsignments();
        return response()->json($expired);
    }

    public function byStatus($status)
    {
        $consignments = $this->transaksiPenitipanUseCase->getConsignmentsByStatus($status);
        return response()->json($consignments);
    }

    public function extend(Request $request, $id)
    {
        $request->validate([
            'additional_days' => 'integer|min:1|max:365'
        ]);

        $additionalDays = $request->get('additional_days', 30);
        $result = $this->transaksiPenitipanUseCase->extendConsignment($id, $additionalDays);

        return $result
            ? response()->json($result)
            : response()->json(['message' => 'Transaksi Penitipan not found'], 404);
    }
}
