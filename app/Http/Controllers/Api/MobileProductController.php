<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\FotoBarang;
use Illuminate\Support\Facades\Log;

class MobileProductController extends Controller
{
    /**
     * Get all products with pagination
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $kategori = $request->get('kategori');
            $search = $request->get('search');
            $status = $request->get('status', 'Tersedia');

            $query = Barang::with(['kategori', 'penitip.user', 'fotoBarang'])
                ->where('status_barang', $status);

            if ($kategori) {
                $query->where('kategori_id', $kategori);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $formattedProducts = $products->map(function($product) {
                return [
                    'id' => $product->barang_id,
                    'nama_barang' => $product->nama_barang,
                    'deskripsi' => $product->deskripsi,
                    'harga' => $product->harga,
                    'kondisi' => $product->kondisi,
                    'status_barang' => $product->status_barang,
                    'kategori' => $product->kategori ? [
                        'id' => $product->kategori->kategori_id,
                        'nama' => $product->kategori->nama_kategori
                    ] : null,
                    'penitip' => $product->penitip ? [
                        'id' => $product->penitip->penitip_id,
                        'nama' => $product->penitip->user->name ?? 'Unknown'
                    ] : null,
                    'foto_utama' => $product->fotoBarang->first() ? 
                        asset('storage/' . $product->fotoBarang->first()->path_foto) : null,
                    'foto_barang' => $product->fotoBarang->map(function($foto) {
                        return asset('storage/' . $foto->path_foto);
                    }),
                    'created_at' => $product->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $formattedProducts,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'has_more' => $products->hasMorePages()
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile products index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data produk'
            ], 500);
        }
    }

    /**
     * Get product detail
     */
    public function show($id)
    {
        try {
            $product = Barang::with(['kategori', 'penitip.user', 'fotoBarang', 'garansi'])
                ->find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            $formattedProduct = [
                'id' => $product->barang_id,
                'nama_barang' => $product->nama_barang,
                'deskripsi' => $product->deskripsi,
                'harga' => $product->harga,
                'kondisi' => $product->kondisi,
                'status_barang' => $product->status_barang,
                'berat' => $product->berat,
                'dimensi' => $product->dimensi,
                'kategori' => $product->kategori ? [
                    'id' => $product->kategori->kategori_id,
                    'nama' => $product->kategori->nama_kategori
                ] : null,
                'penitip' => $product->penitip ? [
                    'id' => $product->penitip->penitip_id,
                    'nama' => $product->penitip->user->name ?? 'Unknown'
                ] : null,
                'foto_barang' => $product->fotoBarang->map(function($foto) {
                    return [
                        'id' => $foto->foto_id,
                        'url' => asset('storage/' . $foto->path_foto),
                        'is_primary' => $foto->is_primary ?? false
                    ];
                }),
                'garansi' => $product->garansi ? [
                    'id' => $product->garansi->garansi_id,
                    'jenis_garansi' => $product->garansi->jenis_garansi,
                    'durasi_garansi' => $product->garansi->durasi_garansi,
                    'syarat_garansi' => $product->garansi->syarat_garansi
                ] : null,
                'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $product->updated_at->format('Y-m-d H:i:s')
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedProduct
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile product show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail produk'
            ], 500);
        }
    }

    /**
     * Get product categories
     */
    public function categories()
    {
        try {
            $categories = KategoriBarang::all()->map(function($category) {
                return [
                    'id' => $category->kategori_id,
                    'nama' => $category->nama_kategori,
                    'deskripsi' => $category->deskripsi
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile categories error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil kategori'
            ], 500);
        }
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q');
            $kategori = $request->get('kategori');
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');
            $kondisi = $request->get('kondisi');
            $perPage = $request->get('per_page', 10);

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query pencarian diperlukan'
                ], 400);
            }

            $searchQuery = Barang::with(['kategori', 'penitip.user', 'fotoBarang'])
                ->where('status_barang', 'Tersedia')
                ->where(function($q) use ($query) {
                    $q->where('nama_barang', 'like', "%{$query}%")
                      ->orWhere('deskripsi', 'like', "%{$query}%");
                });

            if ($kategori) {
                $searchQuery->where('kategori_id', $kategori);
            }

            if ($minPrice) {
                $searchQuery->where('harga', '>=', $minPrice);
            }

            if ($maxPrice) {
                $searchQuery->where('harga', '<=', $maxPrice);
            }

            if ($kondisi) {
                $searchQuery->where('kondisi', $kondisi);
            }

            $products = $searchQuery->orderBy('created_at', 'desc')->paginate($perPage);

            $formattedProducts = $products->map(function($product) {
                return [
                    'id' => $product->barang_id,
                    'nama_barang' => $product->nama_barang,
                    'deskripsi' => $product->deskripsi,
                    'harga' => $product->harga,
                    'kondisi' => $product->kondisi,
                    'status_barang' => $product->status_barang,
                    'kategori' => $product->kategori ? [
                        'id' => $product->kategori->kategori_id,
                        'nama' => $product->kategori->nama_kategori
                    ] : null,
                    'penitip' => $product->penitip ? [
                        'id' => $product->penitip->penitip_id,
                        'nama' => $product->penitip->user->name ?? 'Unknown'
                    ] : null,
                    'foto_utama' => $product->fotoBarang->first() ? 
                        asset('storage/' . $product->fotoBarang->first()->path_foto) : null,
                    'created_at' => $product->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $formattedProducts,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'has_more' => $products->hasMorePages()
                    ],
                    'search_query' => $query
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mobile product search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari produk'
            ], 500);
        }
    }
}
