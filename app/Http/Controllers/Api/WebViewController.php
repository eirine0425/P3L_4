<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
use Illuminate\Support\Facades\DB;

class WebViewController extends Controller
{
    // Halaman Publik
    public function home()
    {
        $featuredProducts = Barang::with(['kategori', 'penitip'])
            ->where('status', 'belum_terjual')
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
        try {
            $barang = Barang::with(['kategoriBarang', 'penitip', 'garansi', 'diskusi.user'])
                ->findOrFail($id);
        
            // Get related products from the same category
            $relatedProducts = Barang::with(['kategoriBarang', 'penitip'])
                ->where('kategori_id', $barang->kategori_id)
                ->where('barang_id', '!=', $id)
                ->where('status', 'belum_terjual')
                ->limit(4)
                ->get();
        
            return view('products.show', compact('barang', 'relatedProducts'));
        } catch (\Exception $e) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak ditemukan.');
        }
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
        {
            $message = 'Garansi dengan nomor seri tersebut tidak ditemukan.';
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
            'keranjang_id' => 'required|exists:keranjang_belanja,keranjang_id'
        ]);
        
        KeranjangBelanja::where('keranjang_id', $request->cart_id)
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

    /**
     * Show checkout page with selected items
     */
    public function showCheckout(Request $request)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->route('dashboard.buyer')->with('error', 'Data pembeli tidak ditemukan');
            }

            // Get selected items from session or request
            $selectedItems = session('checkout_selected_items', []);
            
            if (empty($selectedItems)) {
                $selectedItems = $request->input('selected_items', []);
            }

            if (empty($selectedItems)) {
                return redirect()->route('cart.index')->with('error', 'Pilih minimal satu item untuk checkout');
            }

            // Get cart items that are selected
            $cartItems = KeranjangBelanja::with(['barang.kategori'])
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->whereIn('keranjang_id', $selectedItems)
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Item yang dipilih tidak ditemukan');
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function($item) {
                return $item->barang->harga * $item->jumlah;
            });

            // Shipping calculation: Free if > 1,500,000, otherwise 100,000
            $shippingCost = $subtotal > 1500000 ? 0 : 100000;
            
            // Admin fee
            $adminFee = 2500;
            
            // Total
            $total = $subtotal + $shippingCost + $adminFee;

            // FIXED: Get user addresses - INI YANG PENTING!
            $alamats = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                ->orderBy('status_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get default address
            $defaultAlamat = $alamats->where('status_default', 'Y')->first();
            
            // FIXED: Set alamat terpilih
            $alamatTerpilih = $defaultAlamat ? $defaultAlamat->alamat_id : null;

            return view('checkout.show', compact(
                'cartItems',
                'subtotal',
                'shippingCost',
                'adminFee',
                'total',
                'alamats',
                'defaultAlamat',
                'selectedItems',
                'alamatTerpilih'  // TAMBAHKAN INI
            ));

        } catch (\Exception $e) {
            Log::error('Error in showCheckout: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat memuat halaman checkout');
        }
    }
    
    // Tambahkan method berikut di bawah method showCheckout

    /**
     * Show payment countdown page
     */
    public function showPaymentCountdown($id)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan');
            }

            $transaction = Transaksi::with(['details.barang', 'alamat'])
                ->where('transaksi_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->firstOrFail();

            // Check if transaction is already expired
            if ($transaction->batas_pembayaran && now()->gt($transaction->batas_pembayaran)) {
                return redirect()->route('checkout.cancel', ['transaksi_id' => $id])
                    ->with('error', 'Batas waktu pembayaran telah berakhir');
            }

            return view('checkout.payment', compact('transaction'));

        } catch (\Exception $e) {
            Log::error('Payment countdown error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan');
        }
    }

    /**
     * Check transaction status
     */
    public function checkTransactionStatus($id)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pembeli tidak ditemukan'
                ], 404);
            }

            $transaction = Transaksi::where('transaksi_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            // Check if transaction is expired
            $isExpired = $transaction->batas_pembayaran && now()->gt($transaction->batas_pembayaran);
            $remainingTime = $isExpired ? 0 : now()->diffInSeconds($transaction->batas_pembayaran);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transaksi_id' => $transaction->transaksi_id,
                    'status' => $transaction->status_transaksi,
                    'is_expired' => $isExpired,
                    'remaining_time' => $remainingTime,
                    'batas_pembayaran' => $transaction->batas_pembayaran
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Check transaction status error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memeriksa status transaksi'
            ], 500);
        }
    }

    /**
     * Cancel transaction (manual or auto)
     */
    public function cancelTransaction($id)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan');
            }

            DB::beginTransaction();

            $transaction = Transaksi::with(['details.barang'])
                ->where('transaksi_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->where('status_transaksi', 'menunggu_pembayaran')
                ->firstOrFail();

            // Update transaction status
            $transaction->status_transaksi = 'batal';
            $transaction->save();

            // Return points to buyer if used
            if ($transaction->point_digunakan > 0) {
                $pembeli->increment('point', $transaction->point_digunakan);
            }

            // Make products available again - PERBAIKI STATUS
            foreach ($transaction->details as $detail) {
                $detail->barang->update(['status' => 'belum_terjual']); // UBAH KE belum_terjual
            }

            DB::commit();

            return redirect()->route('checkout.cancelled', ['transaksi_id' => $id])
                ->with('info', 'Transaksi telah dibatalkan karena melewati batas waktu pembayaran');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel transaction error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Terjadi kesalahan saat membatalkan transaksi');
        }
    }

    /**
     * Auto cancel expired transactions (API endpoint)
     */
    public function autoCancelExpiredTransaction($id)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaksi::with(['details.barang', 'pembeli'])
                ->where('transaksi_id', $id)
                ->where('status_transaksi', 'menunggu_pembayaran')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi tidak ditemukan atau sudah diproses'
                ], 404);
            }

            // Check if really expired
            if (!$transaction->batas_pembayaran || now()->lte($transaction->batas_pembayaran)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi belum expired'
                ], 400);
            }

            // Update transaction status
            $transaction->status_transaksi = 'batal';
            $transaction->save();

            // Return points to buyer if used
            if ($transaction->point_digunakan > 0) {
                $transaction->pembeli->increment('point', $transaction->point_digunakan);
            }

            // Make products available again
            foreach ($transaction->details as $detail) {
                $detail->barang->update(['status' => 'belum_terjual']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil dibatalkan otomatis'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto cancel transaction error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membatalkan transaksi'
            ], 500);
        }
    }

    /**
     * Show cancelled transaction page
     */
    public function showCancelledPage($id)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan');
            }

            $transaction = Transaksi::with(['details.barang'])
                ->where('transaksi_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->where('status_transaksi', 'batal')
                ->firstOrFail();

            return view('checkout.cancelled', compact('transaction'));

        } catch (\Exception $e) {
            Log::error('Show cancelled page error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan');
        }
    }

    /**
     * Upload payment proof
     */
    public function uploadBuktiPembayaran(Request $request, $id)
    {
        try {
            $request->validate([
                'bukti_pembayaran' => 'required|image|max:2048', // max 2MB
            ]);

            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan');
            }

            $transaction = Transaksi::where('transaksi_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->firstOrFail();

            if ($transaction->status_transaksi != 'menunggu_pembayaran') {
                return redirect()->back()->with('error', 'Status transaksi tidak valid untuk upload bukti pembayaran');
            }

            // Check if still within payment deadline
            if ($transaction->batas_pembayaran && now()->gt($transaction->batas_pembayaran)) {
                return redirect()->route('checkout.cancel', $id)->with('error', 'Batas waktu pembayaran telah berakhir');
            }

            DB::beginTransaction();

            // Upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/bukti_pembayaran'), $fileName);
                
                $transaction->bukti_pembayaran = 'uploads/bukti_pembayaran/' . $fileName;
                $transaction->status_transaksi = 'dikemas'; // Change to dikemas after payment proof uploaded
                $transaction->tanggal_pelunasan = now();
                $transaction->save();

                // Update product status to sold
                foreach ($transaction->details as $detail) {
                    $detail->barang->update(['status' => 'terjual']);
                }
            }

            DB::commit();

            return redirect()->route('checkout.success', $id)
                ->with('success', 'Bukti pembayaran berhasil diupload! Status pesanan berubah menjadi "Dikemas"');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Upload bukti pembayaran error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupload bukti pembayaran')
                ->withInput();
        }
    }

    /**
     * Show success page after payment
     */
    public function showSuccessPage($id)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan');
            }

            $transaction = Transaksi::with(['details.barang'])
                ->where('transaksi_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->where('status_transaksi', 'dikemas')
                ->firstOrFail();

            return view('checkout.success', compact('transaction'));

        } catch (\Exception $e) {
            Log::error('Show success page error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan');
        }
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
        
        return redirect()->route('thank-you', ['transaksi_id' => $transaction->id]);
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

    /**
     * Get selected cart items for checkout (AJAX endpoint)
     */
    public function getSelectedCartItems(Request $request)
    {
        try {
            $request->validate([
                'selected_items' => 'required|array',
                'selected_items.*' => 'integer|exists:keranjang_belanja,keranjang_id'
            ]);

            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pembeli tidak ditemukan'
                ], 404);
            }

            $selectedItems = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->whereIn('keranjang_id', $request->selected_items)
                ->get();

            if ($selectedItems->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item yang dipilih tidak ditemukan'
                ], 404);
            }

            // Calculate subtotal
            $subtotal = $selectedItems->sum(function($item) {
                $barang = $item->barang ?? null;
                if ($barang) {
                    $harga = $barang->harga ?? 0;
                    $jumlah = $item->jumlah ?? 1;
                    return $harga * $jumlah;
                }
                return 0;
            });

            // Format items for response
            $formattedItems = $selectedItems->map(function($item) {
                $barang = $item->barang;
                return [
                    'keranjang_id' => $item->keranjang_id,
                    'jumlah' => $item->jumlah ?? 1,
                    'barang' => [
                        'barang_id' => $barang->barang_id,
                        'nama_barang' => $barang->nama_barang,
                        'harga' => $barang->harga,
                        'foto_barang' => $barang->foto_barang,
                        'kondisi' => $barang->kondisi,
                        'kategori_barang' => [
                            'nama_kategori' => $barang->kategoriBarang->nama_kategori ?? 'Tanpa Kategori'
                        ]
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'items' => $formattedItems,
                    'count' => $selectedItems->count(),
                    'subtotal' => $subtotal
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Get selected cart items error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat item'
            ], 500);
        }
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

            // FIXED: Gunakan pembeli_id yang benar
            $alamats = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                        ->orderBy('status_default', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();
    
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

            // FIXED: Gunakan pembeli_id yang benar
            $existingAlamats = Alamat::where('pembeli_id', $pembeli->pembeli_id)->count();
            $statusDefault = $existingAlamats == 0 ? 'Y' : 'N';

            // Jika user memilih untuk set sebagai default
            if ($request->has('set_default') && $request->set_default == '1') {
                // Reset semua alamat lain menjadi tidak default
                Alamat::where('pembeli_id', $pembeli->pembeli_id)->update(['status_default' => 'N']);
                $statusDefault = 'Y';
            }

            Alamat::create([
                'pembeli_id' => $pembeli->pembeli_id, // FIXED: Gunakan pembeli_id yang benar
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
            // FIXED: Gunakan primary key yang benar
            $alamat = Alamat::where('alamat_id', $id)->first();
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }

            // Check if alamat belongs to current user
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if ($alamat->pembeli_id != $pembeli->pembeli_id) {
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
                'no_telepon' => 'required|string|max:20',
                'alamat' => 'required|string',
                'kota' => 'required|string|max:100',
                'provinsi' => 'required|string|max:100',
                'kode_pos' => 'required|string|max:10',
            ]);

            // FIXED: Gunakan primary key yang benar
            $alamat = Alamat::where('alamat_id', $id)->first();
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }

            // Check if alamat belongs to current user
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if ($alamat->pembeli_id != $pembeli->pembeli_id) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Anda tidak memiliki akses ke alamat ini');
            }

            // Jika user memilih untuk set sebagai default
            if ($request->has('set_default') && $request->set_default == '1') {
                // Reset semua alamat lain menjadi tidak default
                Alamat::where('pembeli_id', $pembeli->pembeli_id)->update(['status_default' => 'N']);
                $statusDefault = 'Y';
            } else {
                $statusDefault = $alamat->status_default; // Keep existing status
            }

            $alamat->update([
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
            // FIXED: Gunakan primary key yang benar
            $alamat = Alamat::where('alamat_id', $id)->first();
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }

            // Check if alamat belongs to current user
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if ($alamat->pembeli_id != $pembeli->pembeli_id) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Anda tidak memiliki akses ke alamat ini');
            }

            // Check if this is the only address
            $alamatCount = Alamat::where('pembeli_id', $pembeli->pembeli_id)->count();
            if ($alamatCount <= 1) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Tidak dapat menghapus alamat terakhir');
            }

            // If deleting default address, set another address as default
            if ($alamat->status_default == 'Y') {
                $nextAlamat = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                                   ->where('alamat_id', '!=', $id)
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
            // FIXED: Gunakan primary key yang benar
            $alamat = Alamat::where('alamat_id', $id)->first();
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }

            // Check if alamat belongs to current user
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if ($alamat->pembeli_id != $pembeli->pembeli_id) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Anda tidak memiliki akses ke alamat ini');
            }

            // Reset semua alamat lain menjadi tidak default
            Alamat::where('pembeli_id', $pembeli->pembeli_id)->update(['status_default' => 'N']);
            
            // Set alamat ini sebagai default
            $alamat->update(['status_default' => 'Y']);

            return redirect()->route('buyer.alamat.index')->with('success', 'Alamat berhasil dijadikan alamat utama');
        } catch (\Exception $e) {
            return redirect()->route('buyer.alamat.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

            // FIXED: Gunakan pembeli_id yang benar
            $defaultAlamat = Alamat::where('pembeli_id', $pembeli->pembeli_id)
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

            // FIXED: Gunakan pembeli_id yang benar
            $alamats = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                            ->orderBy('status_default', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

            return response()->json(['alamats' => $alamats]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get specific address details for selection
     */
    public function alamatSelect(Request $request)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json(['error' => 'Data pembeli tidak ditemukan'], 404);
            }

            // FIXED: Gunakan pembeli_id yang benar
            $alamats = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                            ->orderBy('status_default', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

            // If this is an AJAX request, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'alamats' => $alamats,
                    'html' => view('components.alamat-selector', compact('alamats'))->render()
                ]);
            }

            // Otherwise return the view
            return view('components.alamat-selector', compact('alamats'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get specific address details for selection
     */
    public function alamatGetDetails($id, Request $request)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json(['error' => 'Data pembeli tidak ditemukan'], 404);
            }

            // FIXED: Gunakan primary key yang benar dan pembeli_id yang benar
            $alamat = Alamat::where('alamat_id', $id)
                           ->where('pembeli_id', $pembeli->pembeli_id)
                           ->first();

            if (!$alamat) {
                return response()->json(['error' => 'Alamat tidak ditemukan'], 404);
            }

            return response()->json([
                'success' => true,
                'alamat' => $alamat
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
