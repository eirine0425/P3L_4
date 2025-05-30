<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Penitip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rating;

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
        
        // Barang terbaru
        $recentItems = Barang::with(['kategori'])
            ->where('penitip_id', $penitip->penitip_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Data untuk grafik
        $itemsByStatus = DB::table('barang')
            ->select('status', DB::raw('count(*) as total'))
            ->where('penitip_id', $penitip->penitip_id)
            ->groupBy('status')
            ->get();
        
        return view('dashboard.consignor.index', compact(
            'totalItems', 
            'activeItems', 
            'soldItems', 
            'soldOutItems', 
            'recentItems',
            'itemsByStatus'
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

    public function showRatings(Request $request)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        // Get all ratings for this consignor's items
        $ratings = Rating::whereHas('barang', function($query) use ($penitip) {
            $query->where('penitip_id', $penitip->penitip_id);
        })
        ->with(['barang', 'pembeli', 'transaksi'])
        ->orderBy('created_at', 'desc');
        
        // Filter by rating if provided
        if ($request->has('rating_filter') && $request->rating_filter != '') {
            $ratings->where('rating', $request->rating_filter);
        }
        
        // Filter by item if provided
        if ($request->has('barang_filter') && $request->barang_filter != '') {
            $ratings->where('barang_id', $request->barang_filter);
        }
        
        $ratings = $ratings->paginate(10);
        
        // Get rating statistics
        $ratingStats = [
            'average_rating' => $penitip->average_rating,
            'total_ratings' => $penitip->total_ratings,
            'rating_distribution' => $penitip->rating_distribution,
            'star_display' => $penitip->star_display,
            'rating_text' => $penitip->rating_text,
        ];
        
        // Get items for filter dropdown
        $myItems = Barang::where('penitip_id', $penitip->penitip_id)
                        ->whereHas('ratings')
                        ->get();
        
        return view('dashboard.consignor.ratings.index', compact('ratings', 'ratingStats', 'myItems', 'penitip'));
    }

    public function showRatingDetail($ratingId)
    {
        $user = Auth::user();
        $penitip = Penitip::where('user_id', $user->id)->first();
        
        if (!$penitip) {
            return redirect()->route('home')->with('error', 'Anda belum terdaftar sebagai penitip.');
        }
        
        $rating = Rating::whereHas('barang', function($query) use ($penitip) {
            $query->where('penitip_id', $penitip->penitip_id);
        })
        ->with(['barang', 'pembeli', 'transaksi'])
        ->where('rating_id', $ratingId)
        ->first();
        
        if (!$rating) {
            return redirect()->route('consignor.ratings')->with('error', 'Rating tidak ditemukan.');
        }
        
        return view('dashboard.consignor.ratings.show', compact('rating', 'penitip'));
    }
}
