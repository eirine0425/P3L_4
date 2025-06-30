<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Alamat\AlamatUsecase;
use App\DTOs\Alamat\CreateAlamatRequest;
use App\DTOs\Alamat\UpdateAlamatRequest;
use App\Models\Alamat;
use App\Models\Pembeli;
use App\DTOs\Alamat\GetAlamatPaginationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AlamatController extends Controller
{
    public function __construct(
        protected AlamatUsecase $alamatUsecase
    ) {}

    public function index(GetAlamatPaginationRequest $request)
    {
        return response()->json($this->alamatUsecase->getAll($request));
    }

    public function store(CreateAlamatRequest $request)
    {
        $user = $this->alamatUsecase->create($request);
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $alamat = $this->alamatUsecase->find($id);
        return $alamat ? response()->json($alamat) : response()->json(['message' => 'Alamat not found'], 404);
    }

    public function update(UpdateAlamatRequest $request, $id)
    {
        $alamat = $this->alamatUsecase->find($id);
        if (!$alamat) {
            return response()->json(['message' => 'Alamat not found'], 404);
        }

        $alamat->update($request->validated());

        return response()->json($alamat);
    }

    /**
     * Validate address for checkout
     */
    public function validateForCheckout($id)
    {
        try {
            $user = Auth::user();
            $pembeli = $user->pembeli;
            
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembeli tidak ditemukan'
                ], 404);
            }
            
            $alamat = $pembeli->alamats()->find($id);
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }
            
            // Validasi alamat untuk checkout
            $isValid = true;
            $errors = [];
            
            if (empty($alamat->nama_penerima)) {
                $isValid = false;
                $errors[] = 'Nama penerima tidak boleh kosong';
            }
            
            if (empty($alamat->no_telepon)) {
                $isValid = false;
                $errors[] = 'Nomor telepon tidak boleh kosong';
            }
            
            if (empty($alamat->alamat)) {
                $isValid = false;
                $errors[] = 'Alamat tidak boleh kosong';
            }
            
            if (empty($alamat->kota)) {
                $isValid = false;
                $errors[] = 'Kota tidak boleh kosong';
            }
            
            return response()->json([
                'success' => $isValid,
                'alamat' => $alamat,
                'is_valid' => $isValid,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get formatted address for checkout
     */
    public function getForCheckout($id = null)
    {
        try {
            $user = Auth::user();
            $pembeli = $user->pembeli;
            
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembeli tidak ditemukan'
                ], 404);
            }
            
            // Jika ID tidak disediakan, gunakan alamat default
            if (!$id) {
                $alamat = $pembeli->alamats()
                    ->where('status_default', 'Y')
                    ->first();
                    
                if (!$alamat) {
                    // Jika tidak ada alamat default, ambil alamat pertama
                    $alamat = $pembeli->alamats()->first();
                }
            } else {
                $alamat = $pembeli->alamats()->find($id);
            }
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }
            
            // Format alamat untuk checkout
            $formattedAddress = [
                'alamat_id' => $alamat->alamat_id,
                'nama_penerima' => $alamat->nama_penerima,
                'no_telepon' => $alamat->no_telepon,
                'alamat_lengkap' => $alamat->alamat,
                'kota' => $alamat->kota,
                'provinsi' => $alamat->provinsi,
                'kode_pos' => $alamat->kode_pos,
                'is_default' => $alamat->status_default === 'Y',
                'formatted_text' => "{$alamat->nama_penerima} - {$alamat->alamat}, {$alamat->kota}, {$alamat->provinsi} {$alamat->kode_pos}"
            ];
            
            return response()->json([
                'success' => true,
                'alamat' => $formattedAddress
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate shipping cost based on address and subtotal
     */
    public function calculateShipping($alamatId, $subtotal)
    {
        try {
            $user = Auth::user();
            $pembeli = $user->pembeli;
            
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembeli tidak ditemukan'
                ], 404);
            }
            
            $alamat = $pembeli->alamats()->find($alamatId);
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }
            
            // Logika perhitungan ongkir
            // Contoh: Gratis ongkir jika subtotal >= 1.500.000
            $freeShippingThreshold = 1500000;
            $standardShippingCost = 100000;
            
            $shippingCost = $subtotal >= $freeShippingThreshold ? 0 : $standardShippingCost;
            
            // Bisa ditambahkan logika lain berdasarkan kota, berat, dll
            
            return response()->json([
                'success' => true,
                'shipping_cost' => $shippingCost,
                'is_free_shipping' => $shippingCost === 0,
                'free_shipping_threshold' => $freeShippingThreshold,
                'remaining_for_free' => $shippingCost > 0 ? $freeShippingThreshold - $subtotal : 0
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        return $this->alamatUsecase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }

    public function createWeb()
    {
        return view('dashboard.buyer.alamat.create');
    }

    public function showWeb($id)
    {
        $user = Auth::user();
        $pembeli = $user->pembeli;
        
        $alamat = $pembeli->alamats()->findOrFail($id);
        
        return view('dashboard.buyer.alamat', compact('alamat'));
    }

    public function setDefault($id)
    {
        try {
            $user = Auth::user();
            $pembeli = $user->pembeli;
            
            // FIXED: Gunakan primary key yang benar
            $alamat = $pembeli->alamats()->where('alamat_id', $id)->first();
            
            if (!$alamat) {
                return redirect()->route('buyer.alamat.index')->with('error', 'Alamat tidak ditemukan');
            }
            
            // Set semua alamat pembeli menjadi tidak default
            $pembeli->alamats()->update(['status_default' => 'N']);
            
            // Set alamat yang dipilih menjadi default
            $alamat->update(['status_default' => 'Y']);
            
            return redirect()->route('buyer.alamat.index')->with('success', 'Alamat default berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah alamat default: ' . $e->getMessage());
        }
    }

    /**
     * Get default alamat for current user
     */
    public function getDefault()
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

            // FIXED: Gunakan pembeli_id yang benar
            $alamat = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                ->where('status_default', 'Y')
                ->first();

            if (!$alamat) {
                // If no default address, get the first address
                $alamat = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$alamat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Belum ada alamat tersimpan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'alamat' => $alamat
            ]);

        } catch (\Exception $e) {
            Log::error('Get default alamat error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat alamat default'
            ], 500);
        }
    }

    /**
     * Get all addresses for selection
     */
    public function getForSelection()
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

            // FIXED: Gunakan pembeli_id yang benar
            $alamats = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                ->orderBy('status_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'alamat' => $alamats
            ]);

        } catch (\Exception $e) {
            Log::error('Get alamats for selection error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat daftar alamat'
            ], 500);
        }
    }
}
