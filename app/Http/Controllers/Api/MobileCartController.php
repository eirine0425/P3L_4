<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KeranjangBelanja;
use App\Models\Barang;
use App\Models\Pembeli;
use Illuminate\Support\Facades\Log;

class MobileCartController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembeli tidak ditemukan'
                ], 404);
            }

            $cartItems = KeranjangBelanja::with(['barang.fotoBarang', 'barang.kategori'])
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->get();

            $formattedItems = $cartItems->map(function($item) {
                return [
                    'id' => $item->keranjang_id,
                    'quantity' => $item->jumlah,
                    'subtotal' => $item->subtotal,
                    'barang' => [
                        'id' => $item->barang->barang_id,
                        'nama_barang' => $item->barang->nama_barang,
                        'harga' => $item->barang->harga,
                        'kondisi' => $item->barang->kondisi,
                        'status_barang' => $item->barang->status_barang,
                        'kategori' => $item->barang->kategori ? [
                            'id' => $item->barang->kategori->kategori_id,
                            'nama' => $item->barang->kategori->nama_kategori
                        ] : null,
                        'foto_utama' => $item->barang->fotoBarang->first() ? 
                            asset('storage/' . $item->barang->fotoBarang->first()->path_foto) : null,
                    ],
                    'created_at' => $item->created_at->format('Y-m-d H:i:s')
                ];
            });

            $totalItems = $cartItems->sum('jumlah');
            $totalPrice = $cartItems->sum('subtotal');

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $formattedItems,
                    'summary' => [
                        'total_items' => $totalItems,
                        'total_price' => $totalPrice,
                        'item_count' => $cartItems->count()
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile cart index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil keranjang'
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'barang_id' => 'required|exists:barangs,barang_id',
                'jumlah' => 'required|integer|min:1'
            ]);

            $user = $request->user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembeli tidak ditemukan'
                ], 404);
            }

            $barang = Barang::find($request->barang_id);
            
            if ($barang->status_barang !== 'Tersedia') {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak tersedia'
                ], 400);
            }

            // Check if item already exists in cart
            $existingItem = KeranjangBelanja::where('pembeli_id', $pembeli->pembeli_id)
                ->where('barang_id', $request->barang_id)
                ->first();

            if ($existingItem) {
                // Update quantity
                $existingItem->jumlah += $request->jumlah;
                $existingItem->subtotal = $existingItem->jumlah * $barang->harga;
                $existingItem->save();

                $cartItem = $existingItem;
            } else {
                // Create new cart item
                $cartItem = KeranjangBelanja::create([
                    'pembeli_id' => $pembeli->pembeli_id,
                    'barang_id' => $request->barang_id,
                    'jumlah' => $request->jumlah,
                    'subtotal' => $request->jumlah * $barang->harga
                ]);
            }

            $cartItem->load(['barang.fotoBarang', 'barang.kategori']);

            $formattedItem = [
                'id' => $cartItem->keranjang_id,
                'quantity' => $cartItem->jumlah,
                'subtotal' => $cartItem->subtotal,
                'barang' => [
                    'id' => $cartItem->barang->barang_id,
                    'nama_barang' => $cartItem->barang->nama_barang,
                    'harga' => $cartItem->barang->harga,
                    'kondisi' => $cartItem->barang->kondisi,
                    'foto_utama' => $cartItem->barang->fotoBarang->first() ? 
                        asset('storage/' . $cartItem->barang->fotoBarang->first()->path_foto) : null,
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan ke keranjang',
                'data' => $formattedItem
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Mobile cart store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan ke keranjang'
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'jumlah' => 'required|integer|min:1'
            ]);

            $user = $request->user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            $cartItem = KeranjangBelanja::where('keranjang_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item keranjang tidak ditemukan'
                ], 404);
            }

            $cartItem->jumlah = $request->jumlah;
            $cartItem->subtotal = $cartItem->jumlah * $cartItem->barang->harga;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diupdate',
                'data' => [
                    'id' => $cartItem->keranjang_id,
                    'quantity' => $cartItem->jumlah,
                    'subtotal' => $cartItem->subtotal
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile cart update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate keranjang'
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            $cartItem = KeranjangBelanja::where('keranjang_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item keranjang tidak ditemukan'
                ], 404);
            }

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile cart destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus item'
            ], 500);
        }
    }

    /**
     * Clear all cart items
     */
    public function clear(Request $request)
    {
        try {
            $user = $request->user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            KeranjangBelanja::where('pembeli_id', $pembeli->pembeli_id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile cart clear error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengosongkan keranjang'
            ], 500);
        }
    }
}
