<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;
use App\Models\PickupSchedule;
use Carbon\Carbon;

class ConsignorPickupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:penitip');
    }

    /**
     * Display items that need pickup
     */
    public function index()
    {
        $user = Auth::user();
        $penitip = $user->penitip;

        if (!$penitip) {
            return redirect()->route('dashboard.consignor')->with('error', 'Data penitip tidak ditemukan.');
        }

        // Get items that need pickup (expired and not sold)
        $expiredItems = Barang::where('penitip_id', $penitip->penitip_id)
            ->where('status', '!=', 'terjual')
            ->where('status', '!=', 'diambil_kembali')
            ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
            ->with(['kategoriBarang', 'pickupSchedule'])
            ->orderBy('batas_penitipan', 'asc')
            ->get();

        // Get scheduled pickups
        $scheduledPickups = PickupSchedule::where('penitip_id', $penitip->penitip_id)
            ->where('status', '!=', 'completed')
            ->with(['items'])
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return view('dashboard.consignor.pickup.index', compact('expiredItems', 'scheduledPickups'));
    }

    /**
     * Schedule pickup for items
     */
    public function schedulePickup(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:barang,barang_id',
            'pickup_method' => 'required|in:ambil_sendiri,kirim_kurir',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'nullable|string',
            'pickup_address' => 'required_if:pickup_method,kirim_kurir|string|max:500',
            'contact_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $penitip = $user->penitip;

        if (!$penitip) {
            return response()->json(['error' => 'Data penitip tidak ditemukan.'], 400);
        }

        try {
            // Create pickup schedule
            $pickupSchedule = PickupSchedule::create([
                'penitip_id' => $penitip->penitip_id,
                'pickup_method' => $request->pickup_method,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'pickup_address' => $request->pickup_address,
                'contact_phone' => $request->contact_phone,
                'notes' => $request->notes,
                'status' => 'confirmed',
                'total_items' => count($request->item_ids)
            ]);

            // Update items with pickup schedule
            Barang::whereIn('barang_id', $request->item_ids)
                ->where('penitip_id', $penitip->penitip_id)
                ->update([
                    'pickup_schedule_id' => $pickupSchedule->id,
                    'pickup_requested_at' => now(),
                    'status_pengambilan' => 'dijadwalkan',
                    'metode_pengambilan' => $request->pickup_method
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengambilan barang berhasil dijadwalkan.',
                'pickup_schedule' => $pickupSchedule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat menjadwalkan pengambilan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel pickup schedule
     */
    public function cancelPickup(Request $request, $scheduleId)
    {
        $user = Auth::user();
        $penitip = $user->penitip;

        $pickupSchedule = PickupSchedule::where('id', $scheduleId)
            ->where('penitip_id', $penitip->penitip_id)
            ->where('status', '!=', 'completed')
            ->first();

        if (!$pickupSchedule) {
            return response()->json(['error' => 'Jadwal pengambilan tidak ditemukan.'], 404);
        }

        try {
            // Update pickup schedule status
            $pickupSchedule->update(['status' => 'cancelled']);

            // Reset items pickup status
            Barang::where('pickup_schedule_id', $scheduleId)->update([
                'pickup_schedule_id' => null,
                'pickup_requested_at' => null,
                'status_pengambilan' => 'belum_dijadwalkan',
                'metode_pengambilan' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal pengambilan berhasil dibatalkan.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat membatalkan jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}
