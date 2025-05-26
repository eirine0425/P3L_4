<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\KeranjangBelanja;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Garansi;
use App\Models\Donasi;
use App\Models\RequestDonasi;
use App\Models\Merch;
use App\Models\TransaksiMerch;
use App\Models\TransaksiPenitipan;
use App\Models\Komisi;
use App\Models\Penitip;
use App\Models\Pembeli;
use App\Models\Organisasi;
use App\Models\Pegawai;
use App\Models\Alamat;

class WebViewController extends Controller
{
    // Halaman Publik
    public function home()
    {
        $featuredProducts = Barang::with('kategori')
            ->where('status', 'belum_terjual') // FIXED: Changed from 'tersedia' to 'belum_terjual'
            ->orderBy('rating', 'desc')
            ->take(8)
            ->get();
        $categories = KategoriBarang::all();
        
        return view('home', compact('featuredProducts', 'categories'));
    }
    
    public function products(Request $request)
    {
        // Always load categories first
        $categories = KategoriBarang::all();
        
        $query = Barang::with(['kategori', 'penitip']);
        
        // Filter by categories
        if ($request->has('categories') && !empty($request->categories)) {
            $query->whereHas('kategori', function($q) use ($request) {
                $q->whereIn('nama_kategori', $request->categories);
            });
        }
        
        // Filter by conditions
        if ($request->has('conditions') && !empty($request->conditions)) {
            $query->whereIn('kondisi', $request->conditions);
        }
        
        // Filter by price range
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('harga', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('harga', '<=', $request->max_price);
        }
        
        // Filter by rating
        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', '>=', $request->rating);
        }
        
        // Search by name
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }
        
        // FIXED: Only show available products (changed from 'tersedia' to 'belum_terjual')
        $query->where('status', 'belum_terjual');
        
        // Sort
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('harga', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('harga', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12)->appends($request->query());
        
        return view('products.index', compact('products', 'categories'));
    }
    
    public function productDetail($id)
    {
        $product = Barang::with(['kategori', 'diskusi.user'])->where('barang_id', $id)->firstOrFail();
        $relatedProducts = Barang::where('kategori_id', $product->kategori_id)
            ->where('barang_id', '!=', $id)
            ->where('status', 'belum_terjual') // FIXED: Changed from 'tersedia' to 'belum_terjual'
            ->take(4)
            ->get();
            
        return view('products.show', compact('product', 'relatedProducts'));
    }
    
    public function warrantyCheck(Request $request)
    {
        $warranty = null;
        $message = '';
        
        if ($request->has('serial_number')) {
            $warranty = Garansi::where('serial_number', $request->serial_number)->first();
            
            if (!$warranty) {
                $message = 'Garansi dengan nomor seri tersebut tidak ditemukan.';
            }
        }
        
        return view('warranty.check', compact('warranty', 'message'));
    }
    
    public function about()
    {
        return view('about');
    }
    
    // Halaman Cart & Checkout
    public function cart()
    {
        $cartItems = KeranjangBelanja::with('barang')
            ->where('user_id', Auth::id())
            ->get();
            
        $total = $cartItems->sum(function($item) {
            return $item->barang->harga * $item->jumlah;
        });
        
        return view('cart.index', compact('cartItems', 'total'));
    }
    
    // FIXED: Updated addToCart method
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:barang,barang_id', // FIXED: Changed to barang_id
            'quantity' => 'required|integer|min:1'
        ]);
        
        // Get product and check availability
        $product = Barang::where('barang_id', $request->product_id)->firstOrFail();
        
        // Check if product is available for sale
        if ($product->status !== 'belum_terjual') {
            return redirect()->back()->with('error', 'Produk tidak tersedia untuk dibeli.');
        }
        
        $existingItem = KeranjangBelanja::where('user_id', Auth::id())
            ->where('barang_id', $request->product_id)
            ->first();
            
        if ($existingItem) {
            $existingItem->jumlah += $request->quantity;
            $existingItem->save();
        } else {
            KeranjangBelanja::create([
                'user_id' => Auth::id(),
                'barang_id' => $request->product_id,
                'jumlah' => $request->quantity
            ]);
        }
        
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }
    
    // Remove from cart
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:keranjang_belanja,id'
        ]);
        
        KeranjangBelanja::where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->delete();
            
        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
    
    // Checkout page
    public function checkout()
    {
        $cartItems = KeranjangBelanja::with('barang')
            ->where('user_id', Auth::id())
            ->get();
            
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja Anda kosong.');
        }
        
        $subtotal = $cartItems->sum(function($item) {
            return $item->barang->harga * $item->jumlah;
        });
        
        $tax = $subtotal * 0.1; // 10% tax
        $shipping = 15000; // Flat shipping rate
        $total = $subtotal + $tax + $shipping;
        
        return view('checkout.index', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }
    
    // Process checkout
    public function processCheckout(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'payment_method' => 'required|in:transfer,cod,credit_card'
        ]);
        
        $cartItems = KeranjangBelanja::with('barang')
            ->where('user_id', Auth::id())
            ->get();
            
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja Anda kosong.');
        }
        
        $subtotal = $cartItems->sum(function($item) {
            return $item->barang->harga * $item->jumlah;
        });
        
        $tax = $subtotal * 0.1;
        $shipping = 15000;
        $total = $subtotal + $tax + $shipping;
        
        // Create transaction
        $transaction = Transaksi::create([
            'user_id' => Auth::id(),
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'shipping_address' => $request->address,
            'shipping_cost' => $shipping,
            'tax' => $tax
        ]);
        
        // Create transaction details and update product status
        foreach ($cartItems as $item) {
            DetailTransaksi::create([
                'transaksi_id' => $transaction->id,
                'barang_id' => $item->barang_id,
                'jumlah' => $item->jumlah,
                'harga' => $item->barang->harga,
                'subtotal' => $item->barang->harga * $item->jumlah
            ]);
            
            // ADDED: Update product status to 'terjual' when purchased
            $item->barang->update(['status' => 'terjual']);
        }
        
        // Clear cart
        KeranjangBelanja::where('user_id', Auth::id())->delete();
        
        return redirect()->route('thank-you', ['transaction_id' => $transaction->id]);
    }
    
    // Thank you page
    public function thankYou($transactionId)
    {
        $transaction = Transaksi::with('details.barang')->findOrFail($transactionId);
        
        return view('checkout.thank-you', compact('transaction'));
    }
    public function profilePembeli()
{
    return view('profile.pembeli'); // ganti dengan nama view yang benar
}

    // Dashboard
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        $role = $user->role->nama_role;
        
        // Redirect to appropriate dashboard based on role
        switch ($role) {
            case 'Owner':
                return $this->ownerDashboard();
            case 'Admin':
                return $this->adminDashboard();
            case 'Pegawai Gudang':
                return $this->warehouseDashboard();
            case 'CS':
                return $this->csDashboard();
            case 'Penitip':
                return $this->consignorDashboard();
            case 'Pembeli':
                return $this->buyerDashboard();
            case 'Organisasi':
                return $this->organizationDashboard();
            default:
                return view('dashboard.index');
        }
    }
    
    // Owner Dashboard
    public function ownerDashboard()
    {
        return view('dashboard.owner.index');
    }
    
    public function adminDashboard()
    {
        return view('dashboard.admin.index');
    }
    
    public function warehouseDashboard()
    {
        return view('dashboard.warehouse.index');
    }
    
    public function csDashboard()
    {
        return view('dashboard.cs.index');
    }
    
    public function consignorDashboard()
    {
        return view('dashboard.consignor.index');
    }
    
    public function buyerDashboard()
    {
        return view('dashboard.buyer.index');
    }
    
    public function organizationDashboard()
    {
        return view('dashboard.organization.index');
    }

    // ========== ALAMAT FUNCTIONS - ADDED BELOW ==========
    
    /**
     * Display a listing of addresses for the authenticated buyer
     */
    public function alamatIndex(Request $request)
    {
        try {
            $user = Auth::user();
        
            // Cari data pembeli berdasarkan user_id
            $pembeli = Pembeli::where('user_id', $user->id)->first();
        
            if (!$pembeli) {
                return redirect()->route('dashboard.buyer')->with('error', 'Data pembeli tidak ditemukan');
            }

            // Ambil alamat berdasarkan pembeli_id - TIDAK MENGGUNAKAN PAGINATION
            $alamats = Alamat::where('pembeli_id', $pembeli->id)
                        ->orderBy('status_default', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get(); // GUNAKAN get() BUKAN paginate()
    
            // PASTIKAN RETURN VIEW BUKAN JSON
            return view('dashboard.buyer.alamat.index', compact('alamats'));
                        
        } catch (\Exception $e) {
            return redirect()->route('dashboard.buyer')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new address
     */
    public function alamatCreate()
    {
        return view('dashboard.buyer.alamat.create');
    }

    /**
     * Store a newly created address in storage
     */
    public function alamatStore(Request $request)
    {
        try {
            $request->validate([
                'nama_penerima' => 'required|string|max:255',
                'no_telepon' => 'required|string|max:20',
                'alamat' => 'required|string',
                'kota' => 'required|string|max:100',
                'provinsi' => 'required|string|max:100',
                'kode_pos' => 'required|string|max:10',
            ]);

            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan');
            }

            // Cek apakah ini alamat pertama
            $existingAlamats = Alamat::where('pembeli_id', $pembeli->id)->count();
            $statusDefault = $existingAlamats == 0 ? 'Y' : 'N';

            // Jika user memilih untuk set sebagai default
            if ($request->has('set_default') && $request->set_default == '1') {
                // Reset semua alamat lain menjadi tidak default
                Alamat::where('pembeli_id', $pembeli->id)->update(['status_default' => 'N']);
                $statusDefault = 'Y';
            }

            Alamat::create([
                'pembeli_id' => $pembeli->id,
                'nama_penerima' => $request->nama_penerima,
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
                'status_default' => $statusDefault,
            ]);

            return redirect()->route('buyer.alamat.index')->with('success', 'Alamat berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified address
     */
    public function alamatEdit($id)
    {
        try {
            $alamat = Alamat::find($id);
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }

            // Check if alamat belongs to current user
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if ($alamat->pembeli_id != $pembeli->id) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Anda tidak memiliki akses ke alamat ini');
            }

            return view('dashboard.buyer.alamat.edit', compact('alamat'));
        } catch (\Exception $e) {
            return redirect()->route('buyer.alamat.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified address in storage
     */
    public function alamatUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_penerima' => 'required|string|max:255',
                'no_telepon' => 'required|string|max:20',
                'alamat' => 'required|string',
                'kota' => 'required|string|max:100',
                'provinsi' => 'required|string|max:100',
                'kode_pos' => 'required|string|max:10',
            ]);

            $alamat = Alamat::find($id);
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }

            // Check if alamat belongs to current user
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if ($alamat->pembeli_id != $pembeli->id) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Anda tidak memiliki akses ke alamat ini');
            }

            // Jika user memilih untuk set sebagai default
            if ($request->has('set_default') && $request->set_default == '1') {
                // Reset semua alamat lain menjadi tidak default
                Alamat::where('pembeli_id', $pembeli->id)->update(['status_default' => 'N']);
                $statusDefault = 'Y';
            } else {
                $statusDefault = $alamat->status_default; // Keep existing status
            }

            $alamat->update([
                'nama_penerima' => $request->nama_penerima,
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
                'status_default' => $statusDefault,
            ]);

            return redirect()->route('buyer.alamat.index')->with('success', 'Alamat berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified address from storage
     */
    public function alamatDestroy($id)
    {
        try {
            $alamat = Alamat::find($id);
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }

            // Check if alamat belongs to current user
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if ($alamat->pembeli_id != $pembeli->id) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Anda tidak memiliki akses ke alamat ini');
            }

            // Check if this is the only address
            $alamatCount = Alamat::where('pembeli_id', $pembeli->id)->count();
            if ($alamatCount <= 1) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Tidak dapat menghapus alamat terakhir');
            }

            // If deleting default address, set another address as default
            if ($alamat->status_default == 'Y') {
                $nextAlamat = Alamat::where('pembeli_id', $pembeli->id)
                                   ->where('id', '!=', $id)
                                   ->first();
                if ($nextAlamat) {
                    $nextAlamat->update(['status_default' => 'Y']);
                }
            }

            $alamat->delete();

            return redirect()->route('buyer.alamat.index')->with('success', 'Alamat berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('buyer.alamat.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Set the specified address as default
     */
    public function alamatSetDefault($id)
    {
        try {
            $alamat = Alamat::find($id);
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }

            // Check if alamat belongs to current user
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if ($alamat->pembeli_id != $pembeli->id) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Anda tidak memiliki akses ke alamat ini');
            }

            // Reset semua alamat lain menjadi tidak default
            Alamat::where('pembeli_id', $pembeli->id)->update(['status_default' => 'N']);
            
            // Set alamat ini sebagai default
            $alamat->update(['status_default' => 'Y']);

            return redirect()->route('buyer.alamat.index')->with('success', 'Alamat berhasil dijadikan alamat utama');
        } catch (\Exception $e) {
            return redirect()->route('buyer.alamat.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get addresses for checkout selection (API ONLY)
     */
    public function getAlamatsForCheckout()
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json(['error' => 'Data pembeli tidak ditemukan'], 404);
            }

            $alamats = Alamat::where('pembeli_id', $pembeli->id)
                            ->orderBy('status_default', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

            return response()->json(['alamats' => $alamats]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get default address for current user (API ONLY)
     */
    public function getDefaultAlamat()
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json(['error' => 'Data pembeli tidak ditemukan'], 404);
            }

            $defaultAlamat = Alamat::where('pembeli_id', $pembeli->id)
                                  ->where('status_default', 'Y')
                                  ->first();

            if (!$defaultAlamat) {
                return response()->json(['error' => 'Alamat default tidak ditemukan'], 404);
            }

            return response()->json(['alamat' => $defaultAlamat]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
