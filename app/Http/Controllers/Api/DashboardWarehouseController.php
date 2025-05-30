<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Pengiriman;
use App\Models\KategoriBarang;
use App\Models\Penitip;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashboardWarehouseController extends Controller
{
    public function index(Request $request)
{
    try {
        // Statistik barang
        $totalItems = Barang::count();
        $activeItems = Barang::where('status', 'belum_terjual')->count();
        $soldItems = Barang::where('status', 'terjual')->count();
        $soldOutItems = Barang::where('status', 'sold out')->count();
        $inactiveItems = Barang::where('status', 'Tidak Aktif')->count();
        $pendingItems = Barang::where('status', 'Menunggu Verifikasi')->count();

        // Statistik durasi penitipan
        $expiringSoonItems = Barang::expiringSoon()->count();
        $expiredItems = Barang::expired()->count();
        $needsAttentionItems = Barang::needsAttention()->count();
        $waitingForPickupItems = Barang::where('status', 'belum_terjual')
            ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')->count();

        // Recent items
        $recentItems = Barang::with(['kategori', 'penitip.user'])
            ->orderBy('created_at', 'desc')->limit(5)->get();

        // Pending shipments
        $pendingShipments = Pengiriman::with(['transaksi.pembeli.user', 'alamat'])
            ->where('status_pengiriman', 'Menunggu Pengiriman')
            ->take(5)->get();

        // Chart data (hanya ditampilkan jika tidak sedang search)
        $itemsByCategory = DB::table('barang')
            ->join('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
            ->select('kategori_barang.nama_kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori_barang.nama_kategori')->get();

        $itemsByStatus = DB::table('barang')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->get();

        // Data untuk filter
        $categories = KategoriBarang::all();
        $statusOptions = [
            'belum_terjual' => 'Belum Terjual',
            'terjual' => 'Terjual',
            'sold out' => 'Sold Out',
            'untuk_donasi' => 'Untuk Donasi'
        ];
        $kondisiOptions = [
            'baru' => 'Baru',
            'sangat_layak' => 'Sangat Layak',
            'layak' => 'Layak'
        ];

        // Optional: hasil pencarian
        $searchResults = null;
        if ($request->hasAny(['search', 'status', 'kategori', 'min_price', 'max_price', 'start_date', 'end_date', 'kondisi', 'sort'])) {
            $query = Barang::with(['kategori', 'penitip.user']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%")
                      ->orWhere('barang_id', 'like', "%{$search}%")
                      ->orWhereHas('penitip.user', function($subQ) use ($search) {
                          $subQ->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('kategori', function($subQ) use ($search) {
                          $subQ->where('nama_kategori', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('kategori')) {
                $query->where('kategori_id', $request->kategori);
            }

            if ($request->filled('kondisi')) {
                $query->where('kondisi', $request->kondisi);
            }

            if ($request->filled('min_price')) {
                $query->where('harga', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('harga', '<=', $request->max_price);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            switch ($request->sort) {
                case 'name_asc': $query->orderBy('nama_barang', 'asc'); break;
                case 'name_desc': $query->orderBy('nama_barang', 'desc'); break;
                case 'price_asc': $query->orderBy('harga', 'asc'); break;
                case 'price_desc': $query->orderBy('harga', 'desc'); break;
                case 'oldest': $query->orderBy('created_at', 'asc'); break;
                default: $query->orderBy('created_at', 'desc'); break;
            }

            $searchResults = $query->paginate(10)->appends($request->query());
        }

        return view('dashboard.warehouse.index', compact(
            'totalItems',
            'activeItems',
            'soldItems',
            'soldOutItems',
            'inactiveItems',
            'pendingItems',
            'expiringSoonItems',
            'expiredItems',
            'needsAttentionItems',
            'waitingForPickupItems',
            'recentItems',
            'pendingShipments',
            'itemsByCategory',
            'itemsByStatus',
            'categories',
            'statusOptions',
            'kondisiOptions',
            'searchResults'
        ));

    } catch (\Exception $e) {
        return view('dashboard.warehouse.index', [
            'totalItems' => 0,
            'activeItems' => 0,
            'soldItems' => 0,
            'soldOutItems' => 0,
            'inactiveItems' => 0,
            'pendingItems' => 0,
            'expiringSoonItems' => 0,
            'expiredItems' => 0,
            'needsAttentionItems' => 0,
            'waitingForPickupItems' => 0,
            'recentItems' => collect(),
            'pendingShipments' => collect(),
            'itemsByCategory' => collect(),
            'itemsByStatus' => collect(),
            'categories' => collect(),
            'statusOptions' => [],
            'kondisiOptions' => [],
            'searchResults' => null
        ]);
    }
}



    

    public function exportResults(Request $request)
    {
        $query = Barang::with(['kategori', 'penitip.user']);
        
        // Apply same filters as search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('barang_id', 'like', "%{$search}%")
                  ->orWhereHas('penitip.user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategori', function($subQ) use ($search) {
                      $subQ->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        
        if ($request->filled('min_price')) {
            $query->where('harga', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('harga', '<=', $request->max_price);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $items = $query->get();
        
        $filename = 'barang_titipan_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'ID Barang',
                'Nama Barang',
                'Deskripsi',
                'Harga',
                'Status',
                'Kondisi',
                'Kategori',
                'Penitip',
                'Tanggal Dibuat'
            ]);
            
            // Data
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->barang_id,
                    $item->nama_barang,
                    $item->deskripsi,
                    $item->harga,
                    $item->status,
                    $item->kondisi,
                    $item->kategori->nama_kategori ?? '-',
                    $item->penitip->user->name ?? '-',
                    $item->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:barang,barang_id',
            'bulk_status' => 'required|in:belum_terjual,terjual,sold out,untuk_donasi'
        ]);
        
        $updatedCount = Barang::whereIn('barang_id', $request->item_ids)
            ->update(['status' => $request->bulk_status]);
        
        return redirect()->back()->with('success', "Berhasil mengupdate status {$updatedCount} barang.");
    }

    public function saveSearch(Request $request)
    {
        $request->validate([
            'search_name' => 'required|string|max:255',
            'search_params' => 'required|array'
        ]);
        
        $savedSearches = Session::get('warehouse_saved_searches', []);
        $savedSearches[$request->search_name] = $request->search_params;
        Session::put('warehouse_saved_searches', $savedSearches);
        
        return response()->json(['success' => true]);
    }

    public function getSavedSearches()
    {
        $savedSearches = Session::get('warehouse_saved_searches', []);
        return response()->json($savedSearches);
    }
    
    public function createShippingSchedule(Request $request, $transactionId)
    {
        $request->validate([
            'tanggal_pengiriman' => 'required|date',
            'metode_pengiriman' => 'required|string',
            'alamat_pengiriman' => 'required|string',
            'catatan' => 'nullable|string'
        ]);
        
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Create or update pengiriman record
        $pengiriman = Pengiriman::updateOrCreate(
            ['transaksi_id' => $transactionId],
            [
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                'metode_pengiriman' => $request->metode_pengiriman,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'status_pengiriman' => 'Dijadwalkan',
                'catatan' => $request->catatan
            ]
        );
        
        return redirect()->back()->with('success', 'Jadwal pengiriman berhasil dibuat.');
    }

    public function createPickupSchedule(Request $request, $transactionId)
    {
        $request->validate([
            'tanggal_pengambilan' => 'required|date',
            'jam_pengambilan' => 'required|string',
            'alamat_pengambilan' => 'required|string',
            'catatan' => 'nullable|string'
        ]);
        
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Create or update pengiriman record for pickup
        $pengiriman = Pengiriman::updateOrCreate(
            ['transaksi_id' => $transactionId],
            [
                'tanggal_pengiriman' => $request->tanggal_pengambilan,
                'jam_pengiriman' => $request->jam_pengambilan,
                'metode_pengiriman' => 'Pickup',
                'alamat_pengiriman' => $request->alamat_pengambilan,
                'status_pengiriman' => 'Menunggu Pengambilan',
                'catatan' => $request->catatan
            ]
        );
        
        return redirect()->back()->with('success', 'Jadwal pengambilan berhasil dibuat.');
    }

    public function generateSalesNote($transactionId)
    {
        $transaction = Transaksi::with([
            'pembeli.user', 
            'detailTransaksi.barang',
            'pengiriman'
        ])->findOrFail($transactionId);
        
        return view('dashboard.warehouse.sales-note', compact('transaction'));
    }

    public function confirmItemReceived(Request $request, $transactionId)
    {
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Update pengiriman status
        if ($transaction->pengiriman) {
            $transaction->pengiriman->update([
                'status_pengiriman' => 'Selesai',
                'tanggal_terima' => now()
            ]);
        }
        
        // Update transaction status
        $transaction->update([
            'status_transaksi' => 'Selesai'
        ]);
        
        // Update item status to sold
        foreach ($transaction->detailTransaksi as $detail) {
            $detail->barang->update([
                'status' => 'Terjual'
            ]);
        }
        
        return redirect()->back()->with('success', 'Konfirmasi penerimaan barang berhasil.');
    }

    public function createConsignmentItem()
    {
        $categories = KategoriBarang::all();
        $penitips = Penitip::with('user')->get();
        
        return view('dashboard.warehouse.consignment.create', compact('categories', 'penitips'));
    }

    public function storeConsignmentItem(Request $request)
    {
        $request->validate([
            'penitip_id' => 'required|exists:penitip,penitip_id',
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:kategori_barang,kategori_id',
            'kondisi' => 'required|in:baru,layak,sangat_layak',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $data = $request->all();
        $data['status'] = 'belum_terjual';
        $data['tanggal_penitipan'] = now();
        
        // Handle file upload
        if ($request->hasFile('foto_barang')) {
            $file = $request->file('foto_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('barang', $filename, 'public');
            $data['foto_barang'] = $path;
        }
        
        Barang::create($data);
        
        return redirect()->route('dashboard.warehouse.inventory')
            ->with('success', 'Barang titipan berhasil ditambahkan.');
    }
    
    public function showItem($id)
    {
        $item = Barang::with([
            'kategori', 
            'penitip.user'
        ])->findOrFail($id);
        
        return view('dashboard.warehouse.item-detail', compact('item'));
    }

    public function editItem($id)
    {
        $item = Barang::with(['kategori', 'penitip.user'])->findOrFail($id);
        $categories = KategoriBarang::all();
        $penitips = Penitip::with('user')->get();
        
        return view('dashboard.warehouse.edit-item', compact('item', 'categories', 'penitips'));
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:kategori_barang,kategori_id',
            'kondisi' => 'required|in:baru,layak,sangat_layak',
            'status' => 'required|in:belum_terjual,terjual,sold out,untuk_donasi',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $item = Barang::findOrFail($id);
        $data = $request->except(['foto_barang']);
        
        // Handle file upload
        if ($request->hasFile('foto_barang')) {
            // Delete old image if exists
            if ($item->foto_barang && Storage::disk('public')->exists($item->foto_barang)) {
                Storage::disk('public')->delete($item->foto_barang);
            }
            
            $file = $request->file('foto_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('barang', $filename, 'public');
            $data['foto_barang'] = $path;
        }
        
        $item->update($data);
        
        return redirect()->route('dashboard.warehouse.item.show', $id)
            ->with('success', 'Data barang berhasil diperbarui.');
    }


    public function updateTransactionStatus(Request $request, $transactionId)
    {
        $request->validate([
            'status_transaksi' => 'required|in:Menunggu Pembayaran,Lunas,Dibatalkan,Selesai',
            'status_barang' => 'nullable|in:Aktif,Tidak Aktif,Terjual,Dikembalikan'
        ]);
        
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Update transaction status
        $transaction->update([
            'status_transaksi' => $request->status_transaksi
        ]);
        
        // Update item status if provided
        if ($request->status_barang) {
            foreach ($transaction->detailTransaksi as $detail) {
                $detail->barang->update([
                    'status' => $request->status_barang
                ]);
            }
        }
        
        // Auto-create donation if transaction is cancelled after 2 days
        if ($request->status_transaksi == 'Dibatalkan') {
            $daysSinceOrder = $transaction->created_at->diffInDays(now());
            if ($daysSinceOrder >= 2) {
                // Logic for automatic donation
                foreach ($transaction->detailTransaksi as $detail) {
                    $detail->barang->update([
                        'status' => 'Untuk Donasi'
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui.');
    }

    public function readyShipments(Request $request)
    {
        $query = Transaksi::with(['pembeli.user', 'detailTransaksi', 'pengiriman'])
            ->whereHas('pengiriman', function ($q) {
                $q->whereIn('status_pengiriman', ['Menunggu Pengiriman', 'Dijadwalkan']);
            });

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('transaksi_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('pembeli.user', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->metode) {
            $query->whereHas('pengiriman', function ($q) use ($request) {
                $q->where('metode_pengiriman', $request->metode);
            });
        }

        if ($request->status) {
            $query->whereHas('pengiriman', function ($q) use ($request) {
                $q->where('status_pengiriman', $request->status);
            });
        }

        // Sorting
        if ($request->sort == 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($request->sort == 'customer_asc') {
            $query->orderByRelation('pembeli.user.name', 'asc');
        } elseif ($request->sort == 'customer_desc') {
            $query->orderByRelation('pembeli.user.name', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate(10)->withQueryString();

        return view('warehouse.consignment.shipment-ready', [
            'transactions' => $transactions,
            'totalReady' => $transactions->total(),
            'readyForPickup' => $transactions->filter(fn($t) => $t->pengiriman->metode_pengiriman == 'Pickup')->count(),
            'readyForDelivery' => $transactions->filter(fn($t) => $t->pengiriman->metode_pengiriman == 'Delivery')->count(),
        ]);
    }

    public function itemPickup(Request $request)
    {
        // Get items that are eligible for pickup (expired consignment)
        $query = Barang::with(['kategori', 'penitip.user', 'transaksiPenitipan'])
            ->where('status', '!=', 'diambil_kembali')
            ->where('status', '!=', 'terjual');

        // Filter items that are expired (past 30 days or 60 days after extension)
        $query->where(function($q) {
            $q->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0');
        });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('barang_id', 'like', "%{$search}%")
                  ->orWhereHas('penitip.user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by penitip
        if ($request->filled('penitip')) {
            $query->where('penitip_id', $request->penitip);
        }

        // Filter by category
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter by days expired
        if ($request->filled('days_expired')) {
            $daysExpired = $request->days_expired;
            $query->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) >= ?', [$daysExpired]);
        }

        // Sorting
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('nama_barang', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('nama_barang', 'desc');
                break;
            case 'expired_longest':
                $query->orderByRaw('DATEDIFF(CURDATE(), batas_penitipan) DESC');
                break;
            case 'expired_shortest':
                $query->orderByRaw('DATEDIFF(CURDATE(), batas_penitipan) ASC');
                break;
            default:
                $query->orderByRaw('DATEDIFF(CURDATE(), batas_penitipan) DESC');
        }

        $items = $query->paginate(15)->appends($request->query());
        
        // Get filter options
        $categories = KategoriBarang::all();
        $penitips = Penitip::with('user')
            ->whereHas('barang', function($q) {
                $q->where('status', '!=', 'diambil_kembali')
                  ->where('status', '!=', 'terjual')
                  ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0');
            })
            ->get();

        // Statistics
        $totalExpiredItems = Barang::whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
            ->where('status', '!=', 'diambil_kembali')
            ->where('status', '!=', 'terjual')
            ->count();
        
        $itemsExpired30Plus = Barang::whereRaw('DATEDIFF(CURDATE(), batas_penitipan) >= 30')
            ->where('status', '!=', 'diambil_kembali')
            ->where('status', '!=', 'terjual')
            ->count();

        $itemsExpired60Plus = Barang::whereRaw('DATEDIFF(CURDATE(), batas_penitipan) >= 60')
            ->where('status', '!=', 'diambil_kembali')
            ->where('status', '!=', 'terjual')
            ->count();

        return view('dashboard.warehouse.item-pickup', compact(
            'items', 
            'categories', 
            'penitips',
            'totalExpiredItems',
            'itemsExpired30Plus',
            'itemsExpired60Plus'
        ));
    }

    public function confirmItemPickup(Request $request, $id)
    {
        $request->validate([
            'pickup_notes' => 'nullable|string|max:500',
            'pickup_method' => 'required|in:penitip_pickup,courier_delivery,warehouse_storage'
        ]);

        $item = Barang::with(['penitip.user', 'transaksiPenitipan'])->findOrFail($id);
        
        // Check if item is eligible for pickup (expired)
        if ($item->sisa_hari >= 0) {
            return redirect()->back()->with('error', 'Barang belum melewati batas waktu penitipan.');
        }

        // Update item status
        $item->update([
            'status' => 'diambil_kembali',
            'tanggal_pengambilan' => now(),
            'catatan_pengambilan' => $request->pickup_notes,
            'metode_pengambilan' => $request->pickup_method
        ]);

        // Update transaksi penitipan if exists
        if ($item->transaksiPenitipan) {
            $item->transaksiPenitipan->update([
                'status_penitipan' => 'selesai_diambil',
                'tanggal_pengambilan' => now(),
                'catatan_pengambilan' => $request->pickup_notes
            ]);
        }

        // Log the pickup activity
        activity()
            ->performedOn($item)
            ->causedBy(auth()->user())
            ->withProperties([
                'pickup_method' => $request->pickup_method,
                'pickup_notes' => $request->pickup_notes,
                'expired_days' => abs($item->sisa_hari)
            ])
            ->log('Item picked up by consignor');

        return redirect()->route('dashboard.warehouse.item-pickup')
            ->with('success', 'Barang berhasil dikonfirmasi untuk pengambilan.');
    }

    public function bulkConfirmPickup(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:barang,barang_id',
            'bulk_pickup_method' => 'required|in:penitip_pickup,courier_delivery,warehouse_storage',
            'bulk_pickup_notes' => 'nullable|string|max:500'
        ]);

        $items = Barang::whereIn('barang_id', $request->item_ids)
            ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
            ->get();

        $updatedCount = 0;
        foreach ($items as $item) {
            $item->update([
                'status' => 'diambil_kembali',
                'tanggal_pengambilan' => now(),
                'catatan_pengambilan' => $request->bulk_pickup_notes,
                'metode_pengambilan' => $request->bulk_pickup_method
            ]);

            // Update transaksi penitipan if exists
            if ($item->transaksiPenitipan) {
                $item->transaksiPenitipan->update([
                    'status_penitipan' => 'selesai_diambil',
                    'tanggal_pengambilan' => now(),
                    'catatan_pengambilan' => $request->bulk_pickup_notes
                ]);
            }

            $updatedCount++;
        }

        return redirect()->back()->with('success', "Berhasil mengkonfirmasi pengambilan {$updatedCount} barang.");
    }

    public function generatePickupReport(Request $request)
    {
        $query = Barang::with(['kategori', 'penitip.user'])
            ->where('status', 'diambil_kembali');

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pengambilan', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pengambilan', '<=', $request->end_date);
        }

        $items = $query->orderBy('tanggal_pengambilan', 'desc')->get();

        $filename = 'laporan_pengambilan_barang_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'ID Barang',
                'Nama Barang',
                'Penitip',
                'Kategori',
                'Harga',
                'Tanggal Penitipan',
                'Batas Penitipan',
                'Tanggal Pengambilan',
                'Metode Pengambilan',
                'Catatan'
            ]);
            
            // Data
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->barang_id,
                    $item->nama_barang,
                    $item->penitip->user->name ?? '-',
                    $item->kategori->nama_kategori ?? '-',
                    $item->harga,
                    $item->tanggal_penitipan,
                    $item->batas_penitipan,
                    $item->tanggal_pengambilan,
                    $item->metode_pengambilan ?? '-',
                    $item->catatan_pengambilan ?? '-'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function showPickupDetail($id)
    {
        $item = Barang::with([
            'kategori', 
            'penitip.user',
            'transaksiPenitipan'
        ])->findOrFail($id);
        
        return view('dashboard.warehouse.pickup-detail', compact('item'));
    }

    // Feature 1: Display list of transactions that need to be sent/picked up
    public function shipments(Request $request)
    {
        $query = Transaksi::with([
            'pembeli.user',
            'detailTransaksi.barang',
            'pengiriman'
        ])->where('status_transaksi', 'Lunas');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'need_shipping') {
                $query->whereDoesntHave('pengiriman');
            } elseif ($request->status === 'in_progress') {
                $query->whereHas('pengiriman', function($q) {
                    $q->whereIn('status_pengiriman', ['Menunggu Pengiriman', 'Dalam Perjalanan']);
                });
            } elseif ($request->status === 'completed') {
                $query->whereHas('pengiriman', function($q) {
                    $q->where('status_pengiriman', 'Terkirim');
                });
            }
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_pesan', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_pesan', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pembeli.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('tanggal_pesan', 'desc')->paginate(15);

        return view('dashboard.warehouse.shipments.index', compact('transactions'));
    }

    public function showShipment($id)
    {
        $transaction = Transaksi::with([
            'pembeli.user',
            'pembeli.alamat',
            'detailTransaksi.barang.penitip.user',
            'pengiriman.pengirim'
        ])->findOrFail($id);

        // Get available couriers
        $couriers = User::whereHas('role', function($q) {
            $q->where('nama_role', 'kurir');
        })->get();

        return view('dashboard.warehouse.shipments.show', compact('transaction', 'couriers'));
    }

    // Feature 2: Add shipping scheduling and courier assignment
    public function createShipment(Request $request, $transactionId)
    {
        $transaction = Transaksi::with(['pembeli.user', 'pembeli.alamat'])->findOrFail($transactionId);
        
        // Get available couriers
        $couriers = User::whereHas('role', function($q) {
            $q->where('nama_role', 'kurir');
        })->get();

        return view('dashboard.warehouse.shipments.create', compact('transaction', 'couriers'));
    }

    public function storeShipment(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksi,transaksi_id',
            'pengirim_id' => 'required|exists:users,id',
            'alamat_id' => 'required|exists:alamat,alamat_id',
            'tanggal_kirim' => 'required|date',
            'nama_penerima' => 'required|string|max:255',
            'catatan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Business rule: Orders after 4 PM cannot be scheduled for the same day
            $orderTime = Carbon::parse($request->tanggal_kirim);
            $currentTime = Carbon::now();
            
            if ($orderTime->isSameDay($currentTime) && $currentTime->hour >= 16) {
                return back()->withErrors([
                    'tanggal_kirim' => 'Pengiriman untuk pembelian di atas jam 4 sore tidak bisa dijadwalkan di hari yang sama.'
                ]);
            }

            // Create shipment
            $pengiriman = Pengiriman::create([
                'pengirim_id' => $request->pengirim_id,
                'transaksi_id' => $request->transaksi_id,
                'alamat_id' => $request->alamat_id,
                'status_pengiriman' => 'Menunggu Pengiriman',
                'tanggal_kirim' => $request->tanggal_kirim,
                'nama_penerima' => $request->nama_penerima,
                'catatan' => $request->catatan
            ]);

            // Send notifications (simplified - in real app you'd use proper notification system)
            $transaction = Transaksi::with(['pembeli.user', 'detailTransaksi.barang.penitip.user'])->find($request->transaksi_id);
            $courier = User::find($request->pengirim_id);

            // Notify buyer
            $this->sendNotification(
                $transaction->pembeli->user,
                'Pesanan Anda akan dikirim',
                "Pesanan Anda dengan ID #{$transaction->transaksi_id} akan dikirim pada " . Carbon::parse($request->tanggal_kirim)->format('d/m/Y') . " oleh kurir {$courier->name}."
            );

            // Notify consignors
            $consignors = $transaction->detailTransaksi->pluck('barang.penitip.user')->unique();
            foreach ($consignors as $consignor) {
                if ($consignor) {
                    $this->sendNotification(
                        $consignor,
                        'Barang Anda akan dikirim',
                        "Barang titipan Anda dalam transaksi #{$transaction->transaksi_id} akan dikirim pada " . Carbon::parse($request->tanggal_kirim)->format('d/m/Y') . "."
                    );
                }
            }

            // Notify courier
            $this->sendNotification(
                $courier,
                'Tugas Pengiriman Baru',
                "Anda mendapat tugas pengiriman untuk transaksi #{$transaction->transaksi_id} pada " . Carbon::parse($request->tanggal_kirim)->format('d/m/Y') . "."
            );

            DB::commit();

            return redirect()->route('warehouse.shipments.show', $request->transaksi_id)
                ->with('success', 'Pengiriman berhasil dijadwalkan dan notifikasi telah dikirim.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menjadwalkan pengiriman.']);
        }
    }

    public function updateShipmentStatus(Request $request, $id)
    {
        $request->validate([
            'status_pengiriman' => 'required|in:Menunggu Pengiriman,Dalam Perjalanan,Terkirim,Dibatalkan'
        ]);

        try {
            $pengiriman = Pengiriman::with(['transaksi.pembeli.user', 'pengirim'])->findOrFail($id);
            
            $oldStatus = $pengiriman->status_pengiriman;
            $pengiriman->update([
                'status_pengiriman' => $request->status_pengiriman,
                'tanggal_terima' => $request->status_pengiriman === 'Terkirim' ? now() : null
            ]);

            // Send notification about status change
            $this->sendNotification(
                $pengiriman->transaksi->pembeli->user,
                'Status Pengiriman Diperbarui',
                "Status pengiriman pesanan #{$pengiriman->transaksi_id} telah diperbarui dari '{$oldStatus}' menjadi '{$request->status_pengiriman}'."
            );

            return back()->with('success', 'Status pengiriman berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui status.']);
        }
    }

    public function assignCourier(Request $request, $id)
    {
        $request->validate([
            'pengirim_id' => 'required|exists:users,id'
        ]);

        try {
            $pengiriman = Pengiriman::with(['transaksi.pembeli.user'])->findOrFail($id);
            $oldCourier = $pengiriman->pengirim;
            $newCourier = User::findOrFail($request->pengirim_id);

            $pengiriman->update(['pengirim_id' => $request->pengirim_id]);

            // Notify old courier if exists
            if ($oldCourier) {
                $this->sendNotification(
                    $oldCourier,
                    'Tugas Pengiriman Dialihkan',
                    "Tugas pengiriman untuk transaksi #{$pengiriman->transaksi_id} telah dialihkan ke kurir lain."
                );
            }

            // Notify new courier
            $this->sendNotification(
                $newCourier,
                'Tugas Pengiriman Baru',
                "Anda mendapat tugas pengiriman untuk transaksi #{$pengiriman->transaksi_id}."
            );

            // Notify buyer
            $this->sendNotification(
                $pengiriman->transaksi->pembeli->user,
                'Kurir Pengiriman Diperbarui',
                "Kurir untuk pengiriman pesanan #{$pengiriman->transaksi_id} telah diperbarui menjadi {$newCourier->name}."
            );

            return back()->with('success', 'Kurir berhasil ditugaskan.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menugaskan kurir.']);
        }
    }

    public function inventory(Request $request)
    {
        $query = Barang::with(['kategori', 'penitip.user']);

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_id', $request->kategori);
        }
        
        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('penitip.user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter khusus untuk barang yang perlu perhatian
        if ($request->has('filter') && $request->filter == 'needs_attention') {
            $query->needsAttention();
        }
        
        // Pengurutan
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('nama_barang', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('nama_barang', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('harga', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('harga', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $items = $query->paginate(15)->appends($request->query());
        $categories = KategoriBarang::all();
        
        return view('dashboard.warehouse.inventory', compact('items', 'categories'));
    }

    

    public function updateItemStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Tidak Aktif,Menunggu Verifikasi'
        ]);

        try {
            $item = Barang::with(['penitip.user'])->findOrFail($id);
            $oldStatus = $item->status;
            
            $item->update(['status' => $request->status]);

            // Notify consignor about status change
            if ($item->penitip && $item->penitip->user) {
                $this->sendNotification(
                    $item->penitip->user,
                    'Status Barang Diperbarui',
                    "Status barang '{$item->nama_barang}' telah diperbarui dari '{$oldStatus}' menjadi '{$request->status}'."
                );
            }

            return back()->with('success', 'Status barang berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui status.']);
        }
    }

    private function sendNotification($user, $title, $message)
    {
        // In a real application, you would implement proper notification system
        // For now, this is a placeholder for the notification logic
        // You could use Laravel's notification system, email, SMS, etc.
        
        // Example: Log the notification (you can replace this with actual notification sending)
        \Log::info("Notification sent to {$user->email}: {$title} - {$message}");
        
        // You could also store notifications in database for in-app notifications
        // Or send emails, push notifications, etc.
    }
}
