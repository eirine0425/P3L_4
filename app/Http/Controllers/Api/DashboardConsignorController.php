<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Penitip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardConsignorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        // Statistik barang penitip
        $totalItems = Barang::where('penitip_id', $penitip->penitip_id)->count();
        $activeItems = Barang::where('penitip_id', $penitip->penitip_id)
                            ->where('status', 'belum_terjual')->count();
        $soldItems = Barang::where('penitip_id', $penitip->penitip_id)
                          ->where('status', 'terjual')->count();
        $soldOutItems = Barang::where('penitip_id', $penitip->penitip_id)
                             ->where('status', 'sold out')->count();
        
        // Statistik durasi penitipan
        try {
            $expiringSoonItems = Barang::where('penitip_id', $penitip->penitip_id)
                                      ->expiringSoon()->count();
            $expiredItems = Barang::where('penitip_id', $penitip->penitip_id)
                                 ->expired()->count();
        } catch (\Exception $e) {
            $expiringSoonItems = 0;
            $expiredItems = 0;
        }
        
        // Barang terbaru
        $recentItems = Barang::with(['kategori'])
            ->where('penitip_id', $penitip->penitip_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Barang yang perlu perhatian (segera berakhir atau kadaluarsa)
        try {
            $itemsNeedAttention = Barang::with(['kategori'])
                ->where('penitip_id', $penitip->penitip_id)
                ->needsAttention()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $itemsNeedAttention = collect();
        }
        
        // Data untuk grafik
        $itemsByStatus = DB::table('barang')
            ->select('status', DB::raw('count(*) as total'))
            ->where('penitip_id', $penitip->penitip_id)
            ->groupBy('status')
            ->get();
        
        // Data untuk grafik durasi
        try {
            $itemsByDuration = collect([
                (object)['status' => 'safe', 'total' => Barang::where('penitip_id', $penitip->penitip_id)->get()->filter(function($item) { return $item->status_durasi === 'safe'; })->count()],
                (object)['status' => 'caution', 'total' => Barang::where('penitip_id', $penitip->penitip_id)->get()->filter(function($item) { return $item->status_durasi === 'caution'; })->count()],
                (object)['status' => 'warning', 'total' => Barang::where('penitip_id', $penitip->penitip_id)->get()->filter(function($item) { return $item->status_durasi === 'warning'; })->count()],
                (object)['status' => 'expired', 'total' => Barang::where('penitip_id', $penitip->penitip_id)->get()->filter(function($item) { return $item->status_durasi === 'expired'; })->count()],
            ])->filter(function($item) { return $item->total > 0; });
        } catch (\Exception $e) {
            $itemsByDuration = collect();
        }
        
        return view('dashboard.consignor.index', compact(
            'totalItems', 
            'activeItems', 
            'soldItems', 
            'soldOutItems',
            'expiringSoonItems',
            'expiredItems',
            'recentItems',
            'itemsNeedAttention',
            'itemsByStatus',
            'itemsByDuration'
        ));
    }
    
    public function items(Request $request)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        $query = Barang::with(['kategori'])
            ->where('penitip_id', $penitip->penitip_id);
        
        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_id', $request->kategori);
        }
        
        // Filter berdasarkan durasi penitipan
        if ($request->has('durasi') && $request->durasi != '') {
            try {
                switch ($request->durasi) {
                    case 'perlu_perhatian':
                        $query->needsAttention();
                        break;
                    case 'segera_berakhir':
                        $query->expiringSoon();
                        break;
                    case 'kadaluarsa':
                        $query->expired();
                        break;
                }
            } catch (\Exception $e) {
                // Ignore filter if column doesn't exist
            }
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
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $items = $query->paginate(10);
        $categories = KategoriBarang::all();
        
        return view('dashboard.consignor.items.index', compact('items', 'categories'));
    }
    
    public function showItem($id)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        $item = Barang::with(['kategori'])
            ->where('penitip_id', $penitip->penitip_id)
            ->where('barang_id', $id)
            ->firstOrFail();
        
        return view('dashboard.consignor.items.show', compact('item'));
    }
    
    public function createItem()
    {
        $categories = KategoriBarang::all();
        return view('dashboard.consignor.items.create', compact('categories'));
    }
    
    public function storeItem(Request $request)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:kategori_barang,kategori_id',
            'kondisi' => 'required|in:baru,layak,sangat_layak',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $data = $request->all();
        $data['penitip_id'] = $penitip->penitip_id;
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
        
        return redirect()->route('consignor.items')
            ->with('success', 'Barang berhasil ditambahkan.');
    }
    
    public function editItem($id)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        $item = Barang::with(['kategori'])
            ->where('penitip_id', $penitip->penitip_id)
            ->where('barang_id', $id)
            ->firstOrFail();
            
        $categories = KategoriBarang::all();
        
        return view('dashboard.consignor.items.edit', compact('item', 'categories'));
    }
    
    public function updateItem(Request $request, $id)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        $item = Barang::where('penitip_id', $penitip->penitip_id)
            ->where('barang_id', $id)
            ->firstOrFail();
        
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:kategori_barang,kategori_id',
            'kondisi' => 'required|in:baru,layak,sangat_layak',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $data = $request->except(['foto_barang']);
        
        // Handle file upload
        if ($request->hasFile('foto_barang')) {
            // Delete old file if exists
            if ($item->foto_barang && file_exists(storage_path('app/public/' . $item->foto_barang))) {
                unlink(storage_path('app/public/' . $item->foto_barang));
            }
            
            $file = $request->file('foto_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('barang', $filename, 'public');
            $data['foto_barang'] = $path;
        }
        
        $item->update($data);
        
        return redirect()->route('consignor.items.show', $id)
            ->with('success', 'Barang berhasil diperbarui.');
    }
    
    public function destroyItem($id)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        $item = Barang::where('penitip_id', $penitip->penitip_id)
            ->where('barang_id', $id)
            ->firstOrFail();
        
        // Delete file if exists
        if ($item->foto_barang && file_exists(storage_path('app/public/' . $item->foto_barang))) {
            unlink(storage_path('app/public/' . $item->foto_barang));
        }
        
        $item->delete();
        
        return redirect()->route('consignor.items')
            ->with('success', 'Barang berhasil dihapus.');
    }

 public function extendItem($id)
{
    $user = Auth::user();
    $penitip = Penitip::where('user_id', $user->id)->first();

    if (!$penitip) {
        return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
    }

    $item = Barang::where('penitip_id', $penitip->penitip_id)
        ->where('barang_id', $id)
        ->first();

    if (!$item) {
        abort(404);
    }

    // Tambahkan 30 hari ke tanggal batas penitipan yang lama
    $item->batas_penitipan = \Carbon\Carbon::parse($item->batas_penitipan)->addDays(30);
    $item->save();

    return redirect()->route('consignor.items')->with('success', 'Masa penitipan berhasil diperpanjang.');
}

public function updateTransactionStatus(Request $request, $id)
    {
        // Example implementation (replace with your actual logic)
        $transaction = Transaksi::findOrFail($id);
        $transaction->status_transaksi = $request->input('status_transaksi');
        $transaction->save();

        return response()->json(['message' => 'Transaction status updated successfully']);
    }

    public function shipmentsReady(Request $request)
    {
        $query = Transaksi::with([
            'pembeli.user', 
            'detailTransaksi.barang.kategori',
            'pengiriman'
        ])
        ->where('status_transaksi', 'Lunas')
        ->whereHas('pengiriman', function($q) {
            $q->whereIn('status_pengiriman', ['Menunggu Pengiriman', 'Dijadwalkan']);
        });

        // Filter berdasarkan metode pengiriman
        if ($request->filled('metode')) {
            if ($request->metode === 'pickup') {
                $query->whereHas('pengiriman', function($q) {
                    $q->where('metode_pengiriman', 'Pickup');
                });
            } elseif ($request->metode === 'delivery') {
                $query->whereHas('pengiriman', function($q) {
                    $q->where('metode_pengiriman', '!=', 'Pickup');
                });
            }
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->whereHas('pengiriman', function($q) use ($request) {
                $q->where('status_pengiriman', $request->status);
            });
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaksi_id', 'like', "%{$search}%")
                  ->orWhereHas('pembeli.user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Pengurutan
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'customer_asc':
                $query->join('pembeli', 'transaksi.pembeli_id', '=', 'pembeli.pembeli_id')
                      ->join('users', 'pembeli.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc')
                      ->select('transaksi.*');
                break;
            case 'customer_desc':
                $query->join('pembeli', 'transaksi.pembeli_id', '=', 'pembeli.pembeli_id')
                      ->join('users', 'pembeli.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'desc')
                      ->select('transaksi.*');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate(15)->appends($request->query());

        // Statistik
        $totalReady = Transaksi::where('status_transaksi', 'Lunas')
            ->whereHas('pengiriman', function($q) {
                $q->whereIn('status_pengiriman', ['Menunggu Pengiriman', 'Dijadwalkan']);
            })->count();

        $readyForPickup = Transaksi::where('status_transaksi', 'Lunas')
            ->whereHas('pengiriman', function($q) {
                $q->where('metode_pengiriman', 'Pickup')
                  ->whereIn('status_pengiriman', ['Menunggu Pengiriman', 'Dijadwalkan']);
            })->count();

        $readyForDelivery = Transaksi::where('status_transaksi', 'Lunas')
            ->whereHas('pengiriman', function($q) {
                $q->where('metode_pengiriman', '!=', 'Pickup')
                  ->whereIn('status_pengiriman', ['Menunggu Pengiriman', 'Dijadwalkan']);
            })->count();

        return view('dashboard.warehouse.shipments-ready', compact(
            'transactions', 
            'totalReady', 
            'readyForPickup', 
            'readyForDelivery'
        ));
    }

    public function shipmentDetail($transactionId)
    {
        $transaction = Transaksi::with([
            'pembeli.user.alamat',
            'detailTransaksi.barang.kategori',
            'pengiriman'
        ])->findOrFail($transactionId);

        // Get all photos for each item
        $itemsWithPhotos = $transaction->detailTransaksi->map(function($detail) {
            $barang = $detail->barang;
            
            // Get multiple photos
            $photos = collect();
            
            // Add main photo if exists
            if ($barang->foto_barang) {
                $photos->push([
                    'path' => $barang->foto_barang,
                    'is_main' => true
                ]);
            }
            
            // Add additional photos from foto_barang table
            $additionalPhotos = \App\Models\FotoBarang::where('barang_id', $barang->barang_id)->get();
            foreach ($additionalPhotos as $photo) {
                $photos->push([
                    'path' => $photo->path,
                    'is_main' => false
                ]);
            }
            
            // If no photos, add placeholder
            if ($photos->isEmpty()) {
                $photos->push([
                    'path' => '/placeholder.svg?height=200&width=200&text=' . urlencode($barang->nama_barang),
                    'is_main' => true,
                    'is_placeholder' => true
                ]);
            }
            
            $detail->photos = $photos;
            return $detail;
        });

        return view('dashboard.warehouse.shipment-detail', compact('transaction', 'itemsWithPhotos'));
    }

    public function updateShipmentStatus(Request $request, $transactionId)
    {
        $request->validate([
            'status_pengiriman' => 'required|in:Menunggu Pengiriman,Dijadwalkan,Sedang Dikirim,Terkirim,Dibatalkan',
            'tanggal_pengiriman' => 'nullable|date',
            'jam_pengiriman' => 'nullable|string',
            'nomor_resi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string'
        ]);

        $transaction = Transaksi::findOrFail($transactionId);
        
        $updateData = [
            'status_pengiriman' => $request->status_pengiriman,
            'catatan' => $request->catatan
        ];

        if ($request->filled('tanggal_pengiriman')) {
            $updateData['tanggal_pengiriman'] = $request->tanggal_pengiriman;
        }

        if ($request->filled('jam_pengiriman')) {
            $updateData['jam_pengiriman'] = $request->jam_pengiriman;
        }

        if ($request->filled('nomor_resi')) {
            $updateData['nomor_resi'] = $request->nomor_resi;
        }

        // Update status terkirim
        if ($request->status_pengiriman === 'Terkirim') {
            $updateData['tanggal_terima'] = now();
            
            // Update transaction status
            $transaction->update(['status_transaksi' => 'Selesai']);
            
            // Update item status
            foreach ($transaction->detailTransaksi as $detail) {
                $detail->barang->update(['status' => 'terjual']);
            }
        }

        // Update or create pengiriman record
        if ($transaction->pengiriman) {
            $transaction->pengiriman->update($updateData);
        } else {
            $updateData['transaksi_id'] = $transactionId;
            \App\Models\Pengiriman::create($updateData);
        }

        return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }

    public function printShippingLabel($transactionId)
    {
        $transaction = Transaksi::with([
            'pembeli.user',
            'detailTransaksi.barang',
            'pengiriman'
        ])->findOrFail($transactionId);

        return view('dashboard.warehouse.shipping-label', compact('transaction'));
    }

    public function markAsReady(Request $request, $transactionId)
    {
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Create or update pengiriman record
        $pengiriman = $transaction->pengiriman;
        if (!$pengiriman) {
            $pengiriman = new \App\Models\Pengiriman();
            $pengiriman->transaksi_id = $transactionId;
        }
        
        $pengiriman->status_pengiriman = 'Menunggu Pengiriman';
        $pengiriman->tanggal_pengiriman = $request->tanggal_pengiriman ?? now()->addDay();
        $pengiriman->metode_pengiriman = $request->metode_pengiriman ?? 'Reguler';
        $pengiriman->alamat_pengiriman = $request->alamat_pengiriman ?? $transaction->pembeli->user->alamat->first()->alamat_lengkap ?? '';
        $pengiriman->save();

        return redirect()->back()->with('success', 'Transaksi berhasil ditandai siap untuk pengiriman.');
    }


}
