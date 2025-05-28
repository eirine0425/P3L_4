<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\KeranjangBelanja\KeranjangBelanjaUseCase;
use App\DTOs\KeranjangBelanja\CreateKeranjangBelanjaRequest;
use App\DTOs\KeranjangBelanja\UpdateKeranjangBelanjaRequest;
use App\DTOs\KeranjangBelanja\GetKeranjangBelanjaPaginationRequest;
use App\Models\KeranjangBelanja;
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KeranjangBelanjaController extends Controller
{
    public function __construct(protected KeranjangBelanjaUseCase $keranjangBelanjaUseCase) {}

    /**
     * Display the user's shopping cart (Web View)
     */
    public function viewCart()
    {
        try {
            // Check if user is authenticated using web guard
            if (!Auth::guard('web')->check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }
            
            $user = Auth::guard('web')->user();
            
            Log::info('=== VIEWING CART ===');
            Log::info('User info', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role->nama_role ?? 'No Role'
            ]);
            
            // Try to find pembeli profile, if not found use user_id directly
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
            
            Log::info('Pembeli info for cart view', [
                'pembeli_found' => $pembeli ? true : false,
                'pembeli_id' => $pembeliId,
                'pembeli_data' => $pembeli ? $pembeli->toArray() : null
            ]);
            
            // Debug: Check all cart items in database
            $allCartItems = KeranjangBelanja::all();
            Log::info('All cart items in database', [
                'total_count' => $allCartItems->count(),
                'all_items' => $allCartItems->toArray()
            ]);
            
            // Get cart items for this user
            $cartItems = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                ->where('pembeli_id', $pembeliId)
                ->get();
            
            Log::info('Cart items for user', [
                'user_pembeli_id' => $pembeliId,
                'count' => $cartItems->count(),
                'items' => $cartItems->toArray()
            ]);
            
            // Also try with user_id directly
            $cartItemsWithUserId = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                ->where('pembeli_id', $user->id)
                ->get();
            
            Log::info('Cart items with user_id', [
                'user_id' => $user->id,
                'count' => $cartItemsWithUserId->count(),
                'items' => $cartItemsWithUserId->toArray()
            ]);
            
            // Use the one that has items, or default to the first query
            if ($cartItemsWithUserId->count() > 0 && $cartItems->count() == 0) {
                $cartItems = $cartItemsWithUserId;
                Log::info('Using cart items with user_id instead');
            }
            
            // Calculate subtotal without quantity (each item has quantity of 1)
            $subtotal = $cartItems->sum(function($item) {
                return $item->barang->harga ?? 0;
            });
            
            // Get recommended products for empty cart
            $recommendedProducts = Barang::where('status', 'belum_terjual')
                ->limit(4)
                ->get();
            
            Log::info('Cart view result', [
                'final_cart_count' => $cartItems->count(),
                'subtotal' => $subtotal,
                'recommended_count' => $recommendedProducts->count()
            ]);
            
            return view('cart.index', compact('cartItems', 'subtotal', 'recommendedProducts'));
            
        } catch (\Exception $e) {
            Log::error('Error viewing cart', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('cart.index', [
                'cartItems' => collect([]),
                'subtotal' => 0,
                'recommendedProducts' => collect([])
            ])->with('error', 'Terjadi kesalahan saat memuat keranjang: ' . $e->getMessage());
        }
    }

    /**
     * Add a product to the shopping cart (Web + API)
     */
    public function store(Request $request)
    {
        try {
            Log::info('=== ADD TO CART REQUEST ===');
            Log::info('Request data', [
                'all_data' => $request->all(),
                'is_json' => $request->expectsJson(),
                'method' => $request->method(),
                'url' => $request->url()
            ]);

            // For web requests, use web guard
            if (!$request->expectsJson()) {
                if (!Auth::guard('web')->check()) {
                    Log::warning('Unauthenticated web request to add cart');
                    return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
                }
                $user = Auth::guard('web')->user();
            } else {
                // For API requests, use default guard
                if (!Auth::check()) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
                }
                $user = Auth::user();
            }
            
            Log::info('User authenticated for add to cart', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role->nama_role ?? 'No Role'
            ]);
            
            // Check user role
            $userRole = strtolower($user->role->nama_role ?? '');
            if ($userRole !== 'pembeli') {
                Log::warning('Non-buyer trying to add to cart', [
                    'user_id' => $user->id,
                    'user_role' => $userRole
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Hanya pembeli yang dapat menambahkan produk ke keranjang'
                    ], 403);
                }
                return redirect()->back()->with('error', 'Hanya pembeli yang dapat menambahkan produk ke keranjang');
            }
            
            // Validate the request
            $request->validate([
                'barang_id' => 'required|integer',
            ]);
            
            $barangId = $request->barang_id;
            
            Log::info('Validating barang', ['barang_id' => $barangId]);
            
            // Check if the item exists and is available
            $barang = Barang::find($barangId);
            if (!$barang) {
                Log::warning('Barang not found', ['barang_id' => $barangId]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Barang tidak ditemukan'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Barang tidak ditemukan');
            }
            
            Log::info('Barang found', [
                'barang_id' => $barang->barang_id,
                'nama_barang' => $barang->nama_barang,
                'status' => $barang->status,
                'harga' => $barang->harga
            ]);
            
            // Check if item is still available (not sold)
            if ($barang->status !== 'belum_terjual') {
                Log::warning('Barang not available', [
                    'barang_id' => $barangId,
                    'status' => $barang->status
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Barang sudah tidak tersedia'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Barang sudah tidak tersedia');
            }
            
            // Try to find pembeli profile
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id; // Use user_id if pembeli not found
            
            Log::info('Pembeli info for add to cart', [
                'pembeli_found' => $pembeli ? true : false,
                'pembeli_id' => $pembeliId,
                'user_id' => $user->id,
                'pembeli_data' => $pembeli ? $pembeli->toArray() : null
            ]);
            
            // Check if product already exists in cart
            $existingItem = KeranjangBelanja::where('pembeli_id', $pembeliId)
                ->where('barang_id', $barangId)
                ->first();
            
            Log::info('Checking existing cart item', [
                'pembeli_id' => $pembeliId,
                'barang_id' => $barangId,
                'existing_item_found' => $existingItem ? true : false,
                'existing_item_data' => $existingItem ? $existingItem->toArray() : null
            ]);
            
            if ($existingItem) {
                Log::info('Item already in cart', [
                    'cart_id' => $existingItem->id,
                    'barang_id' => $barangId
                ]);
                
                // If product exists, return message that it's already in cart
                $message = 'Barang sudah ada dalam keranjang';
                $cartItem = $existingItem;
            } else {
                // Create new cart item
                Log::info('Creating new cart item', [
                    'pembeli_id' => $pembeliId,
                    'barang_id' => $barangId
                ]);
                
                // Use DB transaction to ensure data integrity
                DB::beginTransaction();
                
                try {
                    // Check table structure
                    $tableColumns = DB::select("DESCRIBE keranjang_belanja");
                    Log::info('Table structure', [
                        'columns' => $tableColumns
                    ]);
                    
                    $cartItem = new KeranjangBelanja();
                    $cartItem->pembeli_id = $pembeliId;
                    $cartItem->barang_id = $barangId;
                    $cartItem->save();
                    
                    DB::commit();
                    
                    Log::info('Cart item created successfully', [
                        'cart_id' => $cartItem->id,
                        'pembeli_id' => $pembeliId,
                        'barang_id' => $barangId,
                        'created_at' => $cartItem->created_at,
                        'cart_item_data' => $cartItem->toArray()
                    ]);
                    
                    // Verify the item was actually saved
                    $verifyItem = KeranjangBelanja::find($cartItem->id);
                    Log::info('Verification of saved item', [
                        'found' => $verifyItem ? true : false,
                        'data' => $verifyItem ? $verifyItem->toArray() : null
                    ]);
                    
                    $message = 'Barang berhasil ditambahkan ke keranjang';
                    
                } catch (\Exception $dbError) {
                    DB::rollback();
                    Log::error('Database error creating cart item', [
                        'error' => $dbError->getMessage(),
                        'pembeli_id' => $pembeliId,
                        'barang_id' => $barangId,
                        'trace' => $dbError->getTraceAsString()
                    ]);
                    throw $dbError;
                }
            }
            
            Log::info('Add to cart completed', [
                'success' => true,
                'message' => $message,
                'cart_item_id' => $cartItem->id ?? null
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'data' => $cartItem
                ], 201);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error adding to cart', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource (API)
     */
    public function show($id)
    {
        $keranjangBelanja = $this->keranjangBelanjaUseCase->find($id);
        
        // Check if the cart item belongs to the current user
        $user = Auth::user();
        $pembeli = Pembeli::where('user_id', $user->id)->first();
        $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
        
        if ($keranjangBelanja && $keranjangBelanja->pembeli_id != $pembeliId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return $keranjangBelanja
            ? response()->json($keranjangBelanja)
            : response()->json(['message' => 'Keranjang Belanja not found'], 404);
    }

    /**
     * Update the specified resource in storage (Web + API)
     * Since there's no quantity, this method just returns info that no update is needed
     */
    public function update(Request $request, $id)
    {
        try {
            // For web requests, use web guard
            if (!$request->expectsJson()) {
                if (!Auth::guard('web')->check()) {
                    return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
                }
                $user = Auth::guard('web')->user();
            } else {
                // For API requests, use default guard
                if (!Auth::check()) {
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
                $user = Auth::user();
            }
            
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
            
            // Find the cart item and ensure it belongs to the current user
            $cartItem = KeranjangBelanja::where('id', $id)
                ->where('pembeli_id', $pembeliId)
                ->first();
            
            if (!$cartItem) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Cart item not found'], 404);
                }
                return redirect()->back()->with('error', 'Item keranjang tidak ditemukan');
            }
            
            // Since there's no quantity to update, just return success
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Tidak ada yang perlu diperbarui',
                    'data' => $cartItem
                ]);
            }
            
            return redirect()->back()->with('info', 'Tidak ada yang perlu diperbarui');
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage (Web + API)
     */
    public function destroy($id)
    {
        try {
            $request = request();
            
            // For web requests, use web guard
            if (!$request->expectsJson()) {
                if (!Auth::guard('web')->check()) {
                    return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
                }
                $user = Auth::guard('web')->user();
            } else {
                // For API requests, use default guard
                if (!Auth::check()) {
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
                $user = Auth::user();
            }
            
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
            
            // Find the cart item and ensure it belongs to the current user
            $cartItem = KeranjangBelanja::where('id', $id)
                ->where('pembeli_id', $pembeliId)
                ->first();
            
            if (!$cartItem) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Cart item not found'], 404);
                }
                return redirect()->back()->with('error', 'Item keranjang tidak ditemukan');
            }
            
            // Delete the cart item
            $cartItem->delete();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item berhasil dihapus dari keranjang'
                ]);
            }
            
            return redirect()->back()->with('success', 'Barang berhasil dihapus dari keranjang');
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all items from the shopping cart (Web + API)
     */
    public function clearCart(Request $request)
    {
        try {
            // For web requests, use web guard
            if (!$request->expectsJson()) {
                if (!Auth::guard('web')->check()) {
                    return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
                }
                $user = Auth::guard('web')->user();
            } else {
                // For API requests, use default guard
                if (!Auth::check()) {
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
                $user = Auth::user();
            }
            
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
            
            // Delete all cart items for this user
            KeranjangBelanja::where('pembeli_id', $pembeliId)->delete();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cart cleared successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'Keranjang belanja telah dikosongkan');
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Debug method to check cart data
     */
    public function debug()
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        $pembeli = Pembeli::where('user_id', $user->id)->first();
        $allCartItems = KeranjangBelanja::with(['barang'])->get();
        $userCartItems = KeranjangBelanja::with(['barang'])
            ->where('pembeli_id', $user->id)
            ->get();
        
        if ($pembeli) {
            $pembeliCartItems = KeranjangBelanja::with(['barang'])
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->get();
        } else {
            $pembeliCartItems = collect([]);
        }
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role->nama_role ?? 'No Role'
            ],
            'pembeli' => $pembeli ? $pembeli->toArray() : null,
            'all_cart_items' => $allCartItems->toArray(),
            'user_cart_items' => $userCartItems->toArray(),
            'pembeli_cart_items' => $pembeliCartItems->toArray(),
            'table_structure' => DB::select("DESCRIBE keranjang_belanja")
        ]);
    }

    /**
     * Get selected cart items for checkout
     */
    public function getSelectedItems(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
        
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi',
                    'data' => []
                ], 401);
            }
        
            $pembeliId = $this->getPembeliId($user);
            if (!$pembeliId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profil pembeli tidak ditemukan',
                    'data' => []
                ], 404);
            }
        
            $selectedIds = $request->input('selected_items', []);
        
            if (empty($selectedIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada item yang dipilih',
                    'data' => []
                ], 400);
            }
        
            // Get selected cart items
            $cartItems = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                ->where('pembeli_id', $pembeliId)
                ->whereIn('keranjang_id', $selectedIds)
                ->get();
        
            if ($cartItems->count() === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item yang dipilih tidak ditemukan',
                    'data' => []
                ], 404);
            }
        
            // Calculate subtotal
            $subtotal = $cartItems->sum(function($item) {
                return $item->barang->harga ?? 0;
            });
        
            return response()->json([
                'status' => 'success',
                'message' => 'Item yang dipilih berhasil dimuat',
                'data' => [
                    'items' => $cartItems,
                    'subtotal' => $subtotal,
                    'count' => $cartItems->count()
                ]
            ], 200);
        
        } catch (\Exception $e) {
            Log::error('Error getting selected cart items', [
                'error' => $e->getMessage(),
                'selected_items' => $request->input('selected_items', [])
            ]);
        
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get selected cart items for checkout (alternative route name)
     */
    public function getSelectedItemsForCheckout(Request $request)
    {
        return $this->getSelectedItems($request);
    }

    /**
     * Prepare checkout with selected items
     */
    public function prepareCheckout(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }
        
            $user = Auth::guard('web')->user();
            $pembeliId = $this->getPembeliId($user);
        
            if (!$pembeliId) {
                return redirect()->back()->with('error', 'Profil pembeli tidak ditemukan');
            }
        
            $selectedIds = $request->input('selected_items', []);
        
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'Pilih minimal satu item untuk checkout');
            }
        
            // Get selected cart items
            $cartItems = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                ->where('pembeli_id', $pembeliId)
                ->whereIn('keranjang_id', $selectedIds)
                ->get();
        
            if ($cartItems->count() === 0) {
                return redirect()->back()->with('error', 'Item yang dipilih tidak ditemukan');
            }
        
            // Store selected items in session for checkout process
            session(['checkout_items' => $selectedIds]);
        
            // Redirect to checkout page
            return redirect()->route('checkout.index')->with('success', 'Berhasil memilih ' . $cartItems->count() . ' item untuk checkout');
        
        } catch (\Exception $e) {
            Log::error('Error preparing checkout', [
                'error' => $e->getMessage(),
                'selected_items' => $request->input('selected_items', [])
            ]);
        
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}