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
use Illuminate\Support\Facades\Log;

class DashboardWarehouseController extends Controller
{
    public function index()
    {
        // Statistik barang
        $totalItems = Barang::count();
        $activeItems = Barang::where('status', 'belum_terjual')->count();
        $soldItems = Barang::where('status', 'terjual')->count();
        $soldOutItems = Barang::where('status', 'sold out')->count();
        
        // Barang terbaru
        $recentItems = Barang::with(['kategori', 'penitip.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Data untuk grafik
        $itemsByCategory = DB::table('barang')
            ->join('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
            ->select('kategori_barang.nama_kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori_barang.nama_kategori')
            ->get();
        
        $itemsByStatus = DB::table('barang')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        
        return view('dashboard.warehouse.index', compact(
            'totalItems', 
            'activeItems', 
            'soldItems', 
            'soldOutItems', 
            'recentItems',
            'itemsByCategory',
            'itemsByStatus'
        ));
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
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
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
        
        $items = $query->paginate(10);
        $categories = KategoriBarang::all();
        
        return view('dashboard.warehouse.inventory', compact('items', 'categories'));
    }

    public function createConsignmentItem()
    {
        $categories = KategoriBarang::all();
        $penitips = Penitip::with('user')->get();
        
        return view('dashboard.warehouse.consignment.create', compact('categories', 'penitips'));
    }

    public function storeConsignmentItem(Request $request)
    {
        try {
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
                
                Log::info('File uploaded successfully', [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $path,
                    'full_path' => storage_path('app/public/' . $path)
                ]);
            }
            
            // Ensure penitip_id is set
            if (!$data['penitip_id']) {
                // Get first available penitip or create a default one
                $defaultPenitip = Penitip::first();
                if (!$defaultPenitip) {
                    // Create a default penitip if none exists
                    $defaultPenitip = Penitip::create([
                        'nama' => 'Default Penitip',
                        'user_id' => 1, // Assuming user ID 1 exists
                        'alamat' => 'Alamat Default',
                        'no_telepon' => '000000000'
                    ]);
                }
                $data['penitip_id'] = $defaultPenitip->penitip_id;
            }
            
            Log::info('Creating barang with data:', $data);
            
            $barang = Barang::create($data);
            
            Log::info('Barang created successfully', [
                'barang_id' => $barang->barang_id,
                'penitip_id' => $barang->penitip_id
            ]);
            
            return redirect()->route('dashboard.warehouse.inventory')
                ->with('success', 'Barang titipan berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            Log::error('Error creating consignment item:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function showItem($id)
    {
        try {
            $item = Barang::with([
                'kategori', 
                'penitip.user',
                'garansi',
                'diskusi.user'
            ])->findOrFail($id);

            Log::info('Item loaded for detail view:', [
                'item_id' => $item->barang_id,
                'item_name' => $item->nama_barang,
                'penitip_id' => $item->penitip_id,
                'penitip_loaded' => $item->relationLoaded('penitip'),
                'penitip_exists' => $item->penitip ? true : false,
            ]);

            if ($item->penitip) {
                Log::info('Penitip data found:', [
                    'penitip_id' => $item->penitip->penitip_id,
                    'penitip_nama' => $item->penitip->nama ?? 'NULL',
                    'user_id' => $item->penitip->user_id ?? 'NULL',
                    'user_loaded' => $item->penitip->relationLoaded('user'),
                    'user_exists' => $item->penitip->user ? true : false,
                ]);
            } else {
                Log::warning('No penitip found for item:', [
                    'item_id' => $item->barang_id,
                    'penitip_id_in_item' => $item->penitip_id
                ]);
            }

            return view('dashboard.warehouse.item-detail', compact('item'));
            
        } catch (\Exception $e) {
            Log::error('Error loading item detail:', [
                'item_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('dashboard.warehouse.inventory')
                ->with('error', 'Item tidak ditemukan.');
        }
    }

    public function updateItemStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:belum_terjual,terjual,sold out'
        ]);
        
        $item = Barang::findOrFail($id);
        $item->status = $request->status;
        $item->save();
        
        return redirect()->back()->with('success', 'Status barang berhasil diperbarui.');
    }

    public function transactions(Request $request)
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
                'status' => 'terjual'
            ]);
        }
        
        return redirect()->back()->with('success', 'Konfirmasi penerimaan barang berhasil.');
    }

    public function updateTransactionStatus(Request $request, $transactionId)
    {
        $request->validate([
            'status_transaksi' => 'required|in:Menunggu Pembayaran,Lunas,Dibatalkan,Selesai'
        ]);
        
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Update transaction status
        $transaction->update([
            'status_transaksi' => $request->status_transaksi
        ]);
        
        // Auto-create donation if transaction is cancelled after 2 days
        if ($request->status_transaksi == 'Dibatalkan') {
            $daysSinceOrder = $transaction->created_at->diffInDays(now());
            if ($daysSinceOrder >= 2) {
                // Logic for automatic donation
                foreach ($transaction->detailTransaksi as $detail) {
                    $detail->barang->update([
                        'status' => 'untuk_donasi'
                    ]);
                }
            }
        }
        
        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui.');
    }
}
