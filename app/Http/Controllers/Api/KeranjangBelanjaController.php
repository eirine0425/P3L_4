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
use Illuminate\Support\Facades\Schema;

class KeranjangBelanjaController extends Controller
{
    public function __construct(protected KeranjangBelanjaUseCase $keranjangBelanjaUseCase) {}

    /**
     * Get pembeli ID for the current user
     */
    private function getPembeliId($user)
    {
        $pembeli = Pembeli::where('user_id', $user->id)->first();
        
        if (!$pembeli) {
            Log::warning('Pembeli profile not found for user', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
            return null;
        }
        
        return $pembeli->pembeli_id;
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        try {
            Log::info('=== ADD TO CART REQUEST ===');
            Log::info('Request data', $request->all());

            // Authentication check
            if (!Auth::guard('web')->check()) {
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
                }
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }
            
            $user = Auth::guard('web')->user();
            
            // Role validation
            $userRole = strtolower($user->role->nama_role ?? '');
            if ($userRole !== 'pembeli') {
                $message = 'Hanya pembeli yang dapat menambahkan produk ke keranjang';
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'error', 'message' => $message], 403);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Validate request
            $request->validate([
                'barang_id' => 'required|integer|exists:barang,barang_id',
            ]);
            
            $barangId = $request->barang_id;
            
            // Check if item exists and is available
            $barang = Barang::find($barangId);
            if (!$barang) {
                $message = 'Barang tidak ditemukan';
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'error', 'message' => $message], 404);
                }
                return redirect()->back()->with('error', $message);
            }
            
            if ($barang->status !== 'belum_terjual') {
                $message = 'Barang sudah tidak tersedia';
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'error', 'message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Get pembeli ID
            $pembeliId = $this->getPembeliId($user);
            if (!$pembeliId) {
                $message = 'Profil pembeli belum ditemukan. Silakan lengkapi profil terlebih dahulu.';
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'error', 'message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Check if product already exists in cart
            $existingItem = KeranjangBelanja::where('pembeli_id', $pembeliId)
                ->where('barang_id', $barangId)
                ->first();
            
            if ($existingItem) {
                $message = 'Barang sudah ada dalam keranjang';
                $cartItem = $existingItem;
            } else {
                // Create new cart item
                DB::beginTransaction();
                
                try {
                    // Check if table has timestamp columns
                    $hasTimestamps = Schema::hasColumn('keranjang_belanja', 'created_at') && 
                                   Schema::hasColumn('keranjang_belanja', 'updated_at');
                    
                    $cartData = [
                        'pembeli_id' => $pembeliId,
                        'barang_id' => $barangId,
                    ];
                    
                    if ($hasTimestamps) {
                        $cartData['created_at'] = now();
                        $cartData['updated_at'] = now();
                    }
                    
                    // Insert using raw query to avoid timestamp issues
                    $cartItemId = DB::table('keranjang_belanja')->insertGetId($cartData);
                    
                    // Get the created item
                    $cartItem = KeranjangBelanja::find($cartItemId);
                    
                    DB::commit();
                    
                    Log::info('Cart item created successfully', [
                        'cart_id' => $cartItemId,
                        'pembeli_id' => $pembeliId,
                        'barang_id' => $barangId
                    ]);
                    
                    $message = 'Barang berhasil ditambahkan ke keranjang';
                    
                } catch (\Exception $dbError) {
                    DB::rollback();
                    Log::error('Database error creating cart item', [
                        'error' => $dbError->getMessage(),
                        'pembeli_id' => $pembeliId,
                        'barang_id' => $barangId
                    ]);
                    throw $dbError;
                }
            }
            
            // Handle redirect to cart if requested
            $shouldRedirectToCart = $request->has('redirect_to_cart') || $request->input('redirect_to_cart') == '1';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'data' => $cartItem,
                    'redirect_url' => $shouldRedirectToCart ? route('cart.index') : null
                ], 201);
            }
            
            if ($shouldRedirectToCart) {
                return redirect()->route('cart.index')->with('success', $message);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error adding to cart', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMessage], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }
    }

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
            
            // Get pembeli ID
            $pembeliId = $this->getPembeliId($user);
            if (!$pembeliId) {
                return view('cart.index', [
                    'cartItems' => collect([]),
                    'subtotal' => 0,
                    'recommendedProducts' => Barang::where('status', 'belum_terjual')->limit(4)->get()
                ])->with('error', 'Profil pembeli belum ditemukan. Silakan lengkapi profil terlebih dahulu.');
            }
            
            Log::info('Pembeli info for cart view', [
                'pembeli_id' => $pembeliId,
                'user_id' => $user->id
            ]);
            
            // Debug: Check what's in the database
            $rawCartItems = DB::table('keranjang_belanja')
                ->where('pembeli_id', $pembeliId)
                ->get();
            
            Log::info('Raw cart items from database', [
                'pembeli_id' => $pembeliId,
                'raw_count' => $rawCartItems->count(),
                'raw_items' => $rawCartItems->toArray()
            ]);
            
            // Get cart items for this user with proper relationships
            $cartItems = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                ->where('pembeli_id', $pembeliId)
                ->get();
            
            Log::info('Cart items with relationships', [
                'pembeli_id' => $pembeliId,
                'cart_count' => $cartItems->count(),
                'cart_items' => $cartItems->toArray()
            ]);
            
            // If no items with relationships, try without relationships
            if ($cartItems->count() === 0 && $rawCartItems->count() > 0) {
                Log::warning('Found raw items but no relationships loaded');
                
                // Try to load items manually
                $cartItems = collect();
                foreach ($rawCartItems as $rawItem) {
                    $cartItem = KeranjangBelanja::find($rawItem->keranjang_id);
                    if ($cartItem) {
                        // Try to load barang relationship manually
                        $barang = Barang::find($rawItem->barang_id);
                        if ($barang) {
                            $cartItem->barang = $barang;
                            $cartItems->push($cartItem);
                        }
                    }
                }
                
                Log::info('Manually loaded cart items', [
                    'manual_count' => $cartItems->count(),
                    'manual_items' => $cartItems->toArray()
                ]);
            }
            
            // Calculate subtotal
            $subtotal = $cartItems->sum(function($item) {
                return $item->barang->harga ?? 0;
            });
            
            // Get recommended products for empty cart
            $recommendedProducts = Barang::where('status', 'belum_terjual')
                ->limit(4)
                ->get();
            
            Log::info('Cart view final result', [
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
            
            // Return view with empty data instead of error
            return view('cart.index', [
                'cartItems' => collect([]),
                'subtotal' => 0,
                'recommendedProducts' => collect([])
            ])->with('error', 'Terjadi kesalahan saat memuat keranjang: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource (API)
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $pembeliId = $this->getPembeliId($user);
            
            if (!$pembeliId) {
                return response()->json(['message' => 'Pembeli profile not found'], 404);
            }
            
            $keranjangBelanja = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                ->where('keranjang_id', $id)
                ->where('pembeli_id', $pembeliId)
                ->first();
            
            return $keranjangBelanja
                ? response()->json($keranjangBelanja)
                : response()->json(['message' => 'Keranjang Belanja not found'], 404);
                
        } catch (\Exception $e) {
            Log::error('Error showing cart item', [
                'error' => $e->getMessage(),
                'cart_id' => $id
            ]);
            
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    /**
     * Update the specified resource in storage (Web + API)
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
            
            $pembeliId = $this->getPembeliId($user);
            if (!$pembeliId) {
                $message = 'Profil pembeli tidak ditemukan';
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 404);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Find the cart item and ensure it belongs to the current user
            $cartItem = KeranjangBelanja::where('keranjang_id', $id)
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
            Log::error('Error updating cart item', [
                'error' => $e->getMessage(),
                'cart_id' => $id
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
            
            $pembeliId = $this->getPembeliId($user);
            if (!$pembeliId) {
                $message = 'Profil pembeli tidak ditemukan';
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 404);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Find the cart item and ensure it belongs to the current user
            $cartItem = KeranjangBelanja::where('keranjang_id', $id)
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
            
            Log::info('Cart item deleted successfully', [
                'cart_id' => $id,
                'pembeli_id' => $pembeliId,
                'barang_id' => $cartItem->barang_id
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item berhasil dihapus dari keranjang'
                ]);
            }
            
            return redirect()->back()->with('success', 'Barang berhasil dihapus dari keranjang');
            
        } catch (\Exception $e) {
            Log::error('Error removing cart item', [
                'error' => $e->getMessage(),
                'cart_id' => $id
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
            
            $pembeliId = $this->getPembeliId($user);
            if (!$pembeliId) {
                $message = 'Profil pembeli tidak ditemukan';
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 404);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Delete all cart items for this user
            $deletedCount = KeranjangBelanja::where('pembeli_id', $pembeliId)->delete();
            
            Log::info('Cart cleared successfully', [
                'pembeli_id' => $pembeliId,
                'deleted_count' => $deletedCount
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cart cleared successfully',
                    'deleted_count' => $deletedCount
                ]);
            }
            
            return redirect()->back()->with('success', 'Keranjang belanja telah dikosongkan');
            
        } catch (\Exception $e) {
            Log::error('Error clearing cart', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? Auth::id()
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
     * Get cart count for current user
     */
    public function getCartCount()
    {
        try {
            if (!Auth::guard('web')->check()) {
                return response()->json(['count' => 0]);
            }
            
            $user = Auth::guard('web')->user();
            $pembeliId = $this->getPembeliId($user);
            
            if (!$pembeliId) {
                return response()->json(['count' => 0]);
            }
            
            $count = KeranjangBelanja::where('pembeli_id', $pembeliId)->count();
            
            return response()->json(['count' => $count]);
            
        } catch (\Exception $e) {
            Log::error('Error getting cart count', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null
            ]);
            
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Debug method to check cart data
     */
    public function debug()
    {
        try {
            $user = Auth::guard('web')->user();
            
            if (!$user) {
                return response()->json(['error' => 'Not authenticated']);
            }
            
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $this->getPembeliId($user);
            
            // Get all cart items
            $allCartItems = KeranjangBelanja::with(['barang'])->get();
            
            // Get raw cart items for this user
            $rawUserCartItems = DB::table('keranjang_belanja')
                ->where('pembeli_id', $pembeliId)
                ->get();
            
            // Get cart items with relationships
            $userCartItems = collect([]);
            if ($pembeliId) {
                $userCartItems = KeranjangBelanja::with(['barang'])
                    ->where('pembeli_id', $pembeliId)
                    ->get();
            }
            
            // Check barang table
            $allBarang = Barang::all();
            
            // Check if barang exists for cart items
            $barangCheck = [];
            foreach ($rawUserCartItems as $cartItem) {
                $barang = Barang::find($cartItem->barang_id);
                $barangCheck[] = [
                    'cart_item_id' => $cartItem->keranjang_id,
                    'barang_id' => $cartItem->barang_id,
                    'barang_exists' => $barang ? true : false,
                    'barang_data' => $barang ? $barang->toArray() : null
                ];
            }
            
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role->nama_role ?? 'No Role'
                ],
                'pembeli' => $pembeli ? $pembeli->toArray() : null,
                'pembeli_id_used' => $pembeliId,
                'all_cart_items' => $allCartItems->toArray(),
                'raw_user_cart_items' => $rawUserCartItems->toArray(),
                'user_cart_items_with_relations' => $userCartItems->toArray(),
                'barang_check' => $barangCheck,
                'all_barang_count' => $allBarang->count(),
                'table_structure' => [
                    'keranjang_belanja' => DB::select("DESCRIBE keranjang_belanja"),
                    'barang' => DB::select("DESCRIBE barang")
                ],
                'counts' => [
                    'all_cart' => $allCartItems->count(),
                    'raw_user_cart' => $rawUserCartItems->count(),
                    'user_cart_with_relations' => $userCartItems->count(),
                    'all_barang' => $allBarang->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Debug failed',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
