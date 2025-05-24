<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Pengiriman;
use App\Models\Transaksi;
use App\Models\KategoriBarang;
use App\Models\Penitip;
use Illuminate\Support\Facades\DB;

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
    
    public function updateItemStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:belum_terjual,terjual,sold out'
        ]);
        
        $item = Barang::findOrFail($id);
        $item->status = $request->status;
        $item->save();
        
        return redirect()->route('dashboard.warehouse.item.show', $id)
            ->with('success', 'Status barang berhasil diperbarui.');
    }
}
