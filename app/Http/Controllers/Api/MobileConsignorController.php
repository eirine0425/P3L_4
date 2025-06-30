<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penitip;
use App\Models\Barang;
use App\Models\TransaksiPenitipan;
use Carbon\Carbon;

class MobileConsignorController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();
        $penitip = Penitip::where('user_id', $user->id)->first();

        if (!$penitip) {
            return response()->json([
                'success' => false,
                'message' => 'Data penitip tidak ditemukan',
            ], 404);
        }

        $totalItems = Barang::where('penitip_id', $penitip->id)->count();
        $soldItems = Barang::where('penitip_id', $penitip->id)
            ->where('status', 'terjual')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'penitip' => $penitip,
                'balance' => $penitip->saldo ?? 0,
                'reward_points' => $penitip->poin_reward ?? 0,
                'total_items' => $totalItems,
                'sold_items' => $soldItems,
            ]
        ]);
    }

    public function items(Request $request)
    {
        $user = $request->user();
        $penitip = Penitip::where('user_id', $user->id)->first();

        if (!$penitip) {
            return response()->json([
                'success' => false,
                'message' => 'Data penitip tidak ditemukan',
            ], 404);
        }

        $query = Barang::where('penitip_id', $penitip->id)
            ->with(['kategoriBarang']);

        if ($request->has('status') && $request->status != 'Semua') {
            $query->where('status', strtolower($request->status));
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $items = $query->orderBy('created_at', 'desc')->get();

        $formattedItems = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_barang' => $item->nama_barang,
                'kategori' => $item->kategoriBarang->nama_kategori ?? 'Tidak ada kategori',
                'harga' => $item->harga,
                'status' => $item->status,
                'tanggal_masuk' => $item->created_at,
                'tanggal_terjual' => $item->status == 'terjual' ? $item->updated_at : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedItems,
        ]);
    }

    public function pickup(Request $request)
    {
        $user = $request->user();
        $penitip = Penitip::where('user_id', $user->id)->first();

        if (!$penitip) {
            return response()->json([
                'success' => false,
                'message' => 'Data penitip tidak ditemukan',
            ], 404);
        }

        // Get items that need pickup (expired or requested for pickup)
        $pickupItems = Barang::where('penitip_id', $penitip->id)
            ->where(function ($query) {
                $query->where('status', 'expired')
                      ->orWhere('pickup_requested', true);
            })
            ->with(['kategoriBarang'])
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedItems = $pickupItems->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_barang' => $item->nama_barang,
                'kategori' => $item->kategoriBarang->nama_kategori ?? 'Tidak ada kategori',
                'harga' => $item->harga,
                'status' => $item->status,
                'pickup_requested' => $item->pickup_requested ?? false,
                'pickup_scheduled_date' => $item->pickup_scheduled_date,
                'tanggal_masuk' => $item->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedItems,
        ]);
    }
}
