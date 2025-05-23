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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangBelanjaController extends Controller
{
    public function __construct(protected KeranjangBelanjaUseCase $keranjangBelanjaUseCase) {}

    /**
     * Display the user's shopping cart (Web View)
     */
    public function viewCart()
    {
        // Check if user is authenticated using web guard
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $user = Auth::guard('web')->user();
        $pembeli = Pembeli::where('user_id', $user->id)->first();
        
        if (!$pembeli) {
            return redirect()->route('home')->with('error', 'Profil pembeli tidak ditemukan');
        }
        
        $cartItems = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
            ->where('pembeli_id', $pembeli->pembeli_id)
            ->get();
        
        $total = $cartItems->sum(function($item) {
            return $item->barang->harga * ($item->quantity ?? 1);
        });
        
        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Display a listing of the resource (API)
     */
    public function index(GetKeranjangBelanjaPaginationRequest $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $pembeli = Pembeli::where('user_id', $user->id)->first();
        
        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli profile not found'], 404);
        }
        
        // Get cart items for the current user
        $cartItems = $this->keranjangBelanjaUseCase->getAll($request);
        
        return response()->json($cartItems);
    }

    /**
     * Add a product to the shopping cart (Web + API)
     */
    public function store(Request $request)
    {
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
        
        if (!$pembeli) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pembeli profile not found'], 404);
            }
            return redirect()->back()->with('error', 'Profil pembeli tidak ditemukan');
        }
        
        // Validate the request
        $request->validate([
            'barang_id' => 'required|exists:barangs,barang_id',
            'quantity' => 'nullable|integer|min:1'
        ]);
        
        $quantity = $request->quantity ?? 1;
        $barangId = $request->barang_id;
        
        // Check if product exists and has stock
        $barang = Barang::find($barangId);
        if (!$barang || $barang->stok < $quantity) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Stok tidak mencukupi'], 400);
            }
            return redirect()->back()->with('error', 'Stok tidak mencukupi');
        }
        
        // Check if product already exists in cart
        $existingItem = KeranjangBelanja::where('pembeli_id', $pembeli->pembeli_id)
            ->where('barang_id', $barangId)
            ->first();
        
        if ($existingItem) {
            // If product exists, increment quantity
            $newQuantity = ($existingItem->quantity ?? 1) + $quantity;
            
            // Check if new quantity exceeds stock
            if ($newQuantity > $barang->stok) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Jumlah melebihi stok yang tersedia'], 400);
                }
                return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia');
            }
            
            $existingItem->quantity = $newQuantity;
            $existingItem->save();
            
            $message = 'Jumlah barang dalam keranjang diperbarui';
            $cartItem = $existingItem;
        } else {
            // Create new cart item
            $cartItem = KeranjangBelanja::create([
                'pembeli_id' => $pembeli->pembeli_id,
                'barang_id' => $barangId,
                'quantity' => $quantity
            ]);
            
            $message = 'Barang berhasil ditambahkan ke keranjang';
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $cartItem
            ], 201);
        }
        
        return redirect()->back()->with('success', $message);
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
        
        if (!$pembeli || ($keranjangBelanja && $keranjangBelanja->pembeli_id != $pembeli->pembeli_id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return $keranjangBelanja
            ? response()->json($keranjangBelanja)
            : response()->json(['message' => 'Keranjang Belanja not found'], 404);
    }

    /**
     * Update the specified resource in storage (Web + API)
     */
    public function update(Request $request, $id)
    {
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
        
        if (!$pembeli) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pembeli profile not found'], 404);
            }
            return redirect()->back()->with('error', 'Profil pembeli tidak ditemukan');
        }
        
        // Validate the request
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        // Find the cart item and ensure it belongs to the current user
        $cartItem = KeranjangBelanja::where('keranjang_id', $id)
            ->where('pembeli_id', $pembeli->pembeli_id)
            ->first();
        
        if (!$cartItem) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cart item not found'], 404);
            }
            return redirect()->back()->with('error', 'Item keranjang tidak ditemukan');
        }
        
        // Check stock availability
        if ($request->quantity > $cartItem->barang->stok) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Jumlah melebihi stok yang tersedia'], 400);
            }
            return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia');
        }
        
        // Update the cart item
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Jumlah barang diperbarui',
                'data' => $cartItem
            ]);
        }
        
        return redirect()->back()->with('success', 'Jumlah barang diperbarui');
    }

    /**
     * Remove the specified resource from storage (Web + API)
     */
    public function destroy($id)
    {
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
        
        if (!$pembeli) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pembeli profile not found'], 404);
            }
            return redirect()->back()->with('error', 'Profil pembeli tidak ditemukan');
        }
        
        // Find the cart item and ensure it belongs to the current user
        $cartItem = KeranjangBelanja::where('keranjang_id', $id)
            ->where('pembeli_id', $pembeli->pembeli_id)
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
    }
    
    /**
     * Clear all items from the shopping cart (Web + API)
     */
    public function clearCart(Request $request)
    {
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
        
        if (!$pembeli) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pembeli profile not found'], 404);
            }
            return redirect()->back()->with('error', 'Profil pembeli tidak ditemukan');
        }
        
        // Delete all cart items for this user
        KeranjangBelanja::where('pembeli_id', $pembeli->pembeli_id)->delete();
        
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cart cleared successfully'
            ]);
        }
        
        return redirect()->back()->with('success', 'Keranjang belanja telah dikosongkan');
    }
}
 