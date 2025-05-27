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

class DashboardWarehouseController extends Controller
{
    public function index(Request $request)
    {
        // Statistik barang
        $totalItems = Barang::count();
        $activeItems = Barang::where('status', 'belum_terjual')->count();
        $soldItems = Barang::where('status', 'terjual')->count();
        $soldOutItems = Barang::where('status', 'sold out')->count();
        
        // Data untuk dropdown
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
        
        // Handle search
        $searchResults = null;
        if ($request->hasAny(['search', 'status', 'kategori', 'min_price', 'max_price', 'start_date', 'end_date', 'kondisi', 'sort'])) {
            $query = Barang::with(['kategori', 'penitip.user']);
            
            // Search text
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
            
            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by category
            if ($request->filled('kategori')) {
                $query->where('kategori_id', $request->kategori);
            }
            
            // Filter by condition
            if ($request->filled('kondisi')) {
                $query->where('kondisi', $request->kondisi);
            }
            
            // Filter by price range
            if ($request->filled('min_price')) {
                $query->where('harga', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('harga', '<=', $request->max_price);
            }
            
            // Filter by date range
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            
            // Sorting
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
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
            
            $searchResults = $query->paginate(10)->appends($request->query());
        }
        
        // Barang terbaru (only when not searching)
        $recentItems = collect();
        if (!$searchResults) {
            $recentItems = Barang::with(['kategori', 'penitip.user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        // Data untuk grafik (only when not searching)
        $itemsByCategory = collect();
        $itemsByStatus = collect();
        if (!$searchResults) {
            $itemsByCategory = DB::table('barang')
                ->join('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
                ->select('kategori_barang.nama_kategori', DB::raw('count(*) as total'))
                ->groupBy('kategori_barang.nama_kategori')
                ->get();
            
            $itemsByStatus = DB::table('barang')
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
        }
        
        return view('dashboard.warehouse.index', compact(
            'totalItems', 
            'activeItems', 
            'soldItems', 
            'soldOutItems', 
            'recentItems',
            'itemsByCategory',
            'itemsByStatus',
            'categories',
            'statusOptions',
            'kondisiOptions',
            'searchResults'
        ));
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
    
    // ... rest of the existing methods remain the same ...
    
    public function inventory(Request $request)
    {
        $query = Transaksi::with(['pembeli.user', 'detailTransaksi.barang', 'pengiriman'])
            ->where('status_transaksi', 'Lunas');

        // Filter berdasarkan status pengiriman
        if ($request->has('shipping_status') && $request->shipping_status != '') {
            $query->whereHas('pengiriman', function($q) use ($request) {
                $q->where('status_pengiriman', $request->shipping_status);
            });
        }
        
        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaksi_id', 'like', "%{$search}%")
                  ->orWhereHas('pembeli.user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('dashboard.warehouse.transactions', compact('transactions'));
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
                'status_barang' => 'Terjual'
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

    public function updateItemStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:belum_terjual,terjual,sold out,untuk_donasi'
        ]);
        
        $item = Barang::findOrFail($id);
        $item->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Status barang berhasil diperbarui.');
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
                    'status_barang' => $request->status_barang
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
                        'status_barang' => 'Untuk Donasi'
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui.');
    }
}
