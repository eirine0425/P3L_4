<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penitip;
use App\Models\PickupSchedule;
use App\Models\PickupLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnsoldItemPickupController extends Controller
{
    /**
     * Penitip: Melakukan konfirmasi bahwa barang akan diambil
     */
    public function confirmPickup(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:barang,barang_id',
            'pickup_method' => 'required|in:self_pickup,courier_delivery',
            'pickup_date' => 'required|date|after:today',
            'pickup_time' => 'required_if:pickup_method,self_pickup',
            'pickup_address' => 'required_if:pickup_method,courier_delivery|string|max:500',
            'contact_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar sebagai penitip.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Verify all items belong to this consignor and are eligible for pickup
            $items = Barang::whereIn('barang_id', $request->item_ids)
                ->where('penitip_id', $penitip->penitip_id)
                ->whereIn('status', ['belum_terjual', 'tidak_laku'])
                ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) >= 0') // Expired items
                ->get();

            if ($items->count() !== count($request->item_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa barang tidak dapat diambil atau tidak ditemukan.'
                ], 400);
            }

            // Create pickup schedule
            $pickupSchedule = PickupSchedule::create([
                'penitip_id' => $penitip->penitip_id,
                'pickup_method' => $request->pickup_method,
                'scheduled_date' => $request->pickup_date,
                'scheduled_time' => $request->pickup_time,
                'pickup_address' => $request->pickup_address,
                'contact_phone' => $request->contact_phone,
                'notes' => $request->notes,
                'status' => 'confirmed',
                'total_items' => $items->count()
            ]);

            // Update item status and link to pickup schedule
            foreach ($items as $item) {
                $item->update([
                    'status' => 'menunggu_pengambilan',
                    'pickup_schedule_id' => $pickupSchedule->id,
                    'tanggal_konfirmasi_pengambilan' => now()
                ]);

                // Log the confirmation
                PickupLog::create([
                    'barang_id' => $item->barang_id,
                    'pickup_schedule_id' => $pickupSchedule->id,
                    'action' => 'confirmed',
                    'performed_by' => $user->id,
                    'notes' => 'Penitip mengkonfirmasi pengambilan barang',
                    'created_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi pengambilan berhasil. Tim gudang akan memproses permintaan Anda.',
                'data' => [
                    'pickup_schedule_id' => $pickupSchedule->id,
                    'pickup_method' => $request->pickup_method,
                    'scheduled_date' => $request->pickup_date,
                    'total_items' => $items->count()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses konfirmasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pegawai Gudang: Mencatat pengambilan barang oleh pemilik
     */
    public function recordPickupCompletion(Request $request, $scheduleId)
    {
        $request->validate([
            'pickup_status' => 'required|in:completed,partially_completed,cancelled',
            'picked_up_items' => 'required_if:pickup_status,completed,partially_completed|array',
            'picked_up_items.*' => 'exists:barang,barang_id',
            'actual_pickup_date' => 'required|date',
            'actual_pickup_time' => 'required',
            'warehouse_staff_notes' => 'nullable|string|max:500',
            'condition_notes' => 'nullable|string|max:500',
            'pickup_receipt_number' => 'nullable|string|max:50'
        ]);

        $user = Auth::user();
        
        // Verify user has warehouse staff role
        if (!$user->hasRole('warehouse_staff')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan tindakan ini.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $pickupSchedule = PickupSchedule::with(['items', 'penitip.user'])->findOrFail($scheduleId);
            
            // Update pickup schedule status
            $pickupSchedule->update([
                'status' => $request->pickup_status,
                'actual_pickup_date' => $request->actual_pickup_date,
                'actual_pickup_time' => $request->actual_pickup_time,
                'warehouse_staff_id' => $user->id,
                'warehouse_staff_notes' => $request->warehouse_staff_notes,
                'pickup_receipt_number' => $request->pickup_receipt_number,
                'completed_at' => now()
            ]);

            if ($request->pickup_status === 'completed' || $request->pickup_status === 'partially_completed') {
                // Update status for picked up items
                $pickedUpItems = Barang::whereIn('barang_id', $request->picked_up_items)
                    ->where('pickup_schedule_id', $scheduleId)
                    ->get();

                foreach ($pickedUpItems as $item) {
                    $item->update([
                        'status' => 'diambil_kembali',
                        'tanggal_pengambilan' => $request->actual_pickup_date,
                        'catatan_pengambilan' => $request->condition_notes
                    ]);

                    // Log the pickup completion
                    PickupLog::create([
                        'barang_id' => $item->barang_id,
                        'pickup_schedule_id' => $scheduleId,
                        'action' => 'picked_up',
                        'performed_by' => $user->id,
                        'notes' => "Barang berhasil diambil. " . ($request->warehouse_staff_notes ?? ''),
                        'created_at' => now()
                    ]);
                }

                // Handle remaining items if partially completed
                if ($request->pickup_status === 'partially_completed') {
                    $remainingItems = Barang::where('pickup_schedule_id', $scheduleId)
                        ->whereNotIn('barang_id', $request->picked_up_items)
                        ->get();

                    foreach ($remainingItems as $item) {
                        $item->update([
                            'status' => 'menunggu_pengambilan',
                            'pickup_schedule_id' => null
                        ]);
                    }
                }
            } else if ($request->pickup_status === 'cancelled') {
                // Reset all items status if cancelled
                $allItems = Barang::where('pickup_schedule_id', $scheduleId)->get();
                foreach ($allItems as $item) {
                    $item->update([
                        'status' => 'tidak_laku',
                        'pickup_schedule_id' => null
                    ]);

                    PickupLog::create([
                        'barang_id' => $item->barang_id,
                        'pickup_schedule_id' => $scheduleId,
                        'action' => 'cancelled',
                        'performed_by' => $user->id,
                        'notes' => "Pengambilan dibatalkan. " . ($request->warehouse_staff_notes ?? ''),
                        'created_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status pengambilan berhasil diperbarui.',
                'data' => [
                    'pickup_schedule_id' => $scheduleId,
                    'status' => $request->pickup_status,
                    'picked_up_items_count' => count($request->picked_up_items ?? []),
                    'actual_pickup_date' => $request->actual_pickup_date
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pengambilan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pickup schedules for warehouse staff
     */
    public function getPickupSchedules(Request $request)
    {
        $query = PickupSchedule::with(['penitip.user', 'items'])
            ->orderBy('scheduled_date', 'asc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        // Filter by pickup method
        if ($request->filled('pickup_method')) {
            $query->where('pickup_method', $request->pickup_method);
        }

        $schedules = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    /**
     * Get pickup schedule details
     */
    public function getPickupScheduleDetail($scheduleId)
    {
        $schedule = PickupSchedule::with([
            'penitip.user',
            'items.kategori',
            'logs.user'
        ])->findOrFail($scheduleId);

        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    /**
     * Get unsold items eligible for pickup (for consignor)
     */
    public function getUnsoldItems(Request $request)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar sebagai penitip.'
            ], 403);
        }

        $query = Barang::with(['kategori'])
            ->where('penitip_id', $penitip->penitip_id)
            ->whereIn('status', ['belum_terjual', 'tidak_laku'])
            ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) >= 0'); // Expired items

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('barang_id', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $items = $query->orderBy('batas_penitipan', 'asc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Get pickup history for consignor
     */
    public function getPickupHistory(Request $request)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar sebagai penitip.'
            ], 403);
        }

        $query = PickupSchedule::with(['items'])
            ->where('penitip_id', $penitip->penitip_id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $history = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
