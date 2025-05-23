<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Pengiriman;
use App\Models\Transaksi;
use App\Models\KategoriBarang;
use Illuminate\Support\Facades\DB;

class DashboardWarehouseController extends Controller
{
    public function index()
    {
        // Statistik barang
        $totalItems = Barang::count();
        $activeItems = Barang::where('status_barang', 'Aktif')->count();
        $inactiveItems = Barang::where('status_barang', 'Tidak Aktif')->count();
        $pendingItems = Barang::where('status_barang', 'Menunggu Verifikasi')->count();
        
        // Barang terbaru
        $recentItems = Barang::with(['kategori', 'penitip.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Pengiriman yang perlu diproses
        $pendingShipments = Pengiriman::with(['transaksi.pembeli.user', 'alamat'])
            ->where('status_pengiriman', 'Menunggu Pengiriman')
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
            ->select('status_barang', DB::raw('count(*) as total'))
            ->groupBy('status_barang')
            ->get();
        
        return view('dashboard.warehouse.index', compact(
            'totalItems', 
            'activeItems', 
            'inactiveItems', 
            'pendingItems', 
            'recentItems', 
            'pendingShipments',
            'itemsByCategory',
            'itemsByStatus'
        ));
    }
    
    public function inventory(Request $request)
    {
        $query = Barang::with(['kategori', 'penitip.user']);
        
        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_barang', $request->status);
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
                  ->orWhere('kode_barang', 'like', "%{$search}%");
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
    
    public function shipments(Request $request)
    {
        $query = Pengiriman::with(['transaksi.pembeli.user', 'alamat']);
        
        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_pengiriman', $request->status);
        }
        
        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('transaksi.pembeli.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        // Pengurutan
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'id_asc':
                    $query->orderBy('pengiriman_id', 'asc');
                    break;
                case 'id_desc':
                    $query->orderBy('pengiriman_id', 'desc');
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
        
        $shipments = $query->paginate(10);
        
        return view('dashboard.warehouse.shipments', compact('shipments'));
    }
    
    public function showShipment($id)
    {
        $shipment = Pengiriman::with([
            'transaksi.pembeli.user', 
            'transaksi.detailTransaksi.barang',
            'alamat'
        ])->findOrFail($id);
        
        return view('dashboard.warehouse.shipment-detail', compact('shipment'));
    }
    
    public function updateShipmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu Pengiriman,Sedang Dikirim,Terkirim,Dibatalkan'
        ]);
        
        $shipment = Pengiriman::findOrFail($id);
        $shipment->status_pengiriman = $request->status;
        
        // Jika status berubah menjadi "Sedang Dikirim", update tanggal kirim
        if ($request->status == 'Sedang Dikirim' && $shipment->tanggal_kirim === null) {
            $shipment->tanggal_kirim = now();
        }
        
        // Jika status berubah menjadi "Terkirim", update tanggal terima
        if ($request->status == 'Terkirim') {
            $shipment->tanggal_terima = now();
        }
        
        $shipment->save();
        
        return redirect()->route('dashboard.warehouse.shipment.show', $id)
            ->with('success', 'Status pengiriman berhasil diperbarui.');
    }
    
    public function showItem($id)
    {
        $item = Barang::with([
            'kategori', 
            'penitip.user', 
            'diskusiProduk.user'
        ])->findOrFail($id);
        
        return view('dashboard.warehouse.item-detail', compact('item'));
    }
    
    public function updateItemStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Tidak Aktif,Menunggu Verifikasi,Terjual,Ditolak'
        ]);
        
        $item = Barang::findOrFail($id);
        $item->status_barang = $request->status;
        $item->save();
        
        return redirect()->route('dashboard.warehouse.item.show', $id)
            ->with('success', 'Status barang berhasil diperbarui.');
    }
}
