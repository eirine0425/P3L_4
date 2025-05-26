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
}
