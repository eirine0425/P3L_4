<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\KeranjangBelanja;
use App\Models\Pembeli;
use App\Models\Barang;

class TransaksiController extends Controller
{
    // Konstanta untuk perhitungan ongkir
    const FREE_SHIPPING_THRESHOLD = 1500000; // Rp 1.500.000
    const STANDARD_SHIPPING_COST = 100000;   // Rp 100.000
    const POINT_RATE = 1000;                 // 1 point = Rp 1.000
    const POINT_EARN_RATE = 10000;          // 1 point per Rp 10.000

    public function store(Request $request)
    {
        try {
            $request->validate([
                'selected_items' => 'required|string',
                'metode_pengiriman' => 'required|in:diantar,diambil',
                'subtotal' => 'required|numeric|min:0',
                'shipping_cost' => 'required|numeric|min:0',
                'point_digunakan' => 'nullable|numeric|min:0',
                'total_harga' => 'required|numeric|min:0',
                'alamat_id' => 'nullable|exists:alamat,alamat_id',
            ]);

            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pembeli tidak ditemukan'
                ], 404);
            }

            // Validasi alamat untuk metode diantar
            if ($request->metode_pengiriman === 'diantar' && !$request->alamat_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Alamat pengiriman wajib diisi untuk metode diantar kurir'
                ], 422);
            }

            $selectedItems = json_decode($request->selected_items, true);
            
            if (empty($selectedItems)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada item yang dipilih'
                ], 422);
            }

            // Get cart items
            $cartItems = KeranjangBelanja::with('barang')
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->whereIn('keranjang_id', $selectedItems)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item yang dipilih tidak ditemukan'
                ], 404);
            }

            // Validasi ketersediaan barang - perbaiki status yang dicek
            foreach ($cartItems as $item) {
                if (!$item->barang || $item->barang->status !== 'belum_terjual') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Salah satu barang tidak tersedia lagi: ' . ($item->barang->nama_barang ?? 'Unknown')
                    ], 422);
                }
            }

            // Hitung subtotal dari item yang dipilih
            $calculatedSubtotal = $cartItems->sum(function($item) {
                return $item->barang->harga * ($item->jumlah ?? 1);
            });

            // Validasi subtotal dari request
            if (abs($calculatedSubtotal - $request->subtotal) > 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Subtotal tidak sesuai dengan perhitungan server'
                ], 422);
            }

            // Hitung ongkos kirim
            $shippingCalculation = $this->calculateShippingCost(
                $request->metode_pengiriman,
                $calculatedSubtotal
            );

            // Validasi ongkos kirim dari request
            $calculatedShipping = (float)$shippingCalculation['cost'];
            $receivedShipping = (float)$request->shipping_cost;
            if (abs($calculatedShipping - $receivedShipping) > 0.01) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ongkos kirim tidak sesuai dengan perhitungan server',
                    'calculated_shipping' => $calculatedShipping,
                    'received_shipping' => $receivedShipping
                ], 422);
            }

            // Validasi point yang digunakan
            $pointUsed = (int)($request->point_digunakan ?? 0);
            if ($pointUsed > 0) {
                if ($pointUsed > $pembeli->point) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Point yang digunakan melebihi point yang tersedia'
                    ], 422);
                }
            }

            // Hitung point discount
            $pointDiscount = $pointUsed * self::POINT_RATE;

            // Hitung total akhir
            $calculatedTotal = $calculatedSubtotal + $shippingCalculation['cost'] - $pointDiscount;

            // Validasi total dari request
            if (abs($calculatedTotal - $request->total_harga) > 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Total harga tidak sesuai dengan perhitungan server',
                    'calculated_total' => $calculatedTotal,
                    'received_total' => $request->total_harga
                ], 422);
            }

            // Hitung point yang diperoleh
            $pointEarned = floor($calculatedSubtotal / self::POINT_EARN_RATE);

            DB::beginTransaction();
            
            try {
                // Create transaction dengan status "menunggu pembayaran"
                $transaction = Transaksi::create([
                    'pembeli_id' => $pembeli->pembeli_id,
                    'alamat_id' => $request->alamat_id,
                    'cs_id' => null, // Akan diisi oleh CS saat memproses pesanan
                    'tanggal_pesan' => now(),
                    'tanggal_pelunasan' => null, // Akan diisi saat pembayaran dikonfirmasi
                    'point_digunakan' => $pointUsed,
                    'point_diperoleh' => $pointEarned,
                    'bukti_pembayaran' => null, // Akan diupload oleh pembeli
                    'metode_pengiriman' => $request->metode_pengiriman,
                    'subtotal' => $calculatedSubtotal,
                    'ongkos_kirim' => $shippingCalculation['cost'],
                    'point_discount' => $pointDiscount,
                    'total_harga' => $calculatedTotal,
                    'status_transaksi' => 'menunggu pembayaran', // Ubah status menjadi menunggu pembayaran
                    'keterangan_ongkir' => $shippingCalculation['description'],
                    'batas_pembayaran' => now()->addMinutes(1), // TAMBAHKAN INI: 1 menit dari sekarang
                ]);

                // Create transaction details
                foreach ($cartItems as $item) {
                    $jumlah = $item->jumlah ?? 1;
                    $harga = $item->barang->harga;
                    
                    DetailTransaksi::create([
                        'transaksi_id' => $transaction->transaksi_id,
                        'barang_id' => $item->barang_id,
                        'jumlah' => $jumlah,
                        'harga' => $harga,
                        'subtotal' => $harga * $jumlah
                    ]);

                    // Jangan update status barang ke terjual dulu, biarkan tetap belum_terjual
                    // Status akan diubah setelah pembayaran dikonfirmasi
                    // $item->barang->update(['status' => 'terjual']);
                }

                // Update pembeli points
                if ($pointUsed > 0) {
                    $pembeli->decrement('point', $pointUsed);
                }

                // Remove items from cart
                KeranjangBelanja::whereIn('keranjang_id', $selectedItems)->delete();

                DB::commit();

                // Redirect directly to payment countdown page
                return redirect()->route('checkout.payment', ['id' => $transaction->transaksi_id])
                    ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran dalam 1 menit.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Database transaction error: ' . $e->getMessage(), [
                    'user_id' => Auth::id(),
                    'exception' => get_class($e),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw to be caught by outer catch
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.',
                'error_details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Calculate shipping cost based on delivery method and subtotal
     */
    private function calculateShippingCost($deliveryMethod, $subtotal)
    {
        $result = [
            'cost' => 0,
            'description' => '',
            'is_free' => false
        ];

        switch ($deliveryMethod) {
            case 'diambil':
                $result['cost'] = 0;
                $result['description'] = 'Ambil sendiri di toko - GRATIS';
                $result['is_free'] = true;
                break;

            case 'diantar':
                if ($subtotal >= self::FREE_SHIPPING_THRESHOLD) {
                    $result['cost'] = 0;
                    $result['description'] = 'Gratis ongkir untuk belanja di atas Rp ' . number_format(self::FREE_SHIPPING_THRESHOLD, 0, ',', '.');
                    $result['is_free'] = true;
                } else {
                    $result['cost'] = self::STANDARD_SHIPPING_COST;
                    $result['description'] = 'Ongkos kirim standar';
                    $result['is_free'] = false;
                }
                break;

            default:
                $result['cost'] = 0;
                $result['description'] = 'Metode pengiriman tidak valid';
                break;
        }

        return $result;
    }

    /**
     * Get shipping cost calculation for AJAX requests
     */
    public function calculateShipping(Request $request)
    {
        try {
            $request->validate([
                'metode_pengiriman' => 'required|in:diantar,diambil',
                'subtotal' => 'required|numeric|min:0'
            ]);

            $shippingCalculation = $this->calculateShippingCost(
                $request->metode_pengiriman,
                $request->subtotal
            );

            return response()->json([
                'status' => 'success',
                'data' => $shippingCalculation
            ]);

        } catch (\Exception $e) {
            Log::error('Calculate shipping error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghitung ongkos kirim'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan');
            }

            $transaction = Transaksi::with(['details.barang.kategoriBarang', 'alamat'])
                ->where('transaksi_id', $id)
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->firstOrFail();

            return view('dashboard.buyer.transactions.show', compact('transaction'));

        } catch (\Exception $e) {
            Log::error('Transaction show error: ' . $e->getMessage());
            return redirect()->route('buyer.transactions')->with('error', 'Transaksi tidak ditemukan');
        }
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan');
            }

            $transactions = Transaksi::with(['details.barang', 'alamat'])
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->orderBy('tanggal_pesan', 'desc')
                ->paginate(10);

            return view('dashboard.buyer.transactions.index', compact('transactions'));

        } catch (\Exception $e) {
            Log::error('Transaction index error: ' . $e->getMessage());
            return view('dashboard.buyer.transactions.index', ['transactions' => collect()]);
        }
    }

    public function uploadProof(Request $request, $transaksi_id)
    {
        // Validate the request
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Find the transaction
        $transaction = Transaksi::findOrFail($transaksi_id);
        
        // Check if payment deadline has passed
        if ($transaction->batas_pembayaran < now()) {
            return redirect()->route('payment.show', $transaksi_id)
                ->with('error', 'Waktu pembayaran telah habis. Transaksi dibatalkan.');
        }
        
        // Store the file
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        
        // Update the transaction
        $transaction->bukti_pembayaran = $path;
        $transaction->status_pembayaran = 'menunggu_konfirmasi';
        $transaction->tanggal_pembayaran = now();
        $transaction->save();
        
        // Redirect with success message
        return redirect()->route('payment.show', $transaksi_id)
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin.');
    }
    
    /**
     * Cancel a transaction
     */
    public function cancelTransaction($transaksi_id)
    {
        // Find the transaction
        $transaction = Transaksi::findOrFail($transaksi_id);
        
        // Update status
        $transaction->status = 'dibatalkan';
        $transaction->save();
        
        // Redirect with message
        return redirect()->route('home')
            ->with('info', 'Transaksi berhasil dibatalkan.');
    }
    
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

            if ($transaction->status_transaksi != 'menunggu pembayaran') {
                return redirect()->back()->with('error', 'Status transaksi tidak valid untuk upload bukti pembayaran');
            }

            // Upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/bukti_pembayaran'), $fileName);
                
                $transaction->bukti_pembayaran = 'uploads/bukti_pembayaran/' . $fileName;
                $transaction->status_transaksi = 'menunggu_konfirmasi';
                $transaction->save();
            }

            return redirect()->route('buyer.transactions.show', $id)
                ->with('success', 'Bukti pembayaran berhasil diupload');

        } catch (\Exception $e) {
            Log::error('Upload bukti pembayaran error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupload bukti pembayaran')
                ->withInput();
        }
    }

    /**
     * Get shipping info for frontend
     */
    public function getShippingInfo()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'free_shipping_threshold' => self::FREE_SHIPPING_THRESHOLD,
                'standard_shipping_cost' => self::STANDARD_SHIPPING_COST,
                'point_rate' => self::POINT_RATE,
                'point_earn_rate' => self::POINT_EARN_RATE,
                'delivery_methods' => [
                    'diambil' => [
                        'name' => 'Ambil Sendiri',
                        'description' => 'Ambil di toko - GRATIS',
                        'cost' => 0
                    ],
                    'diantar' => [
                        'name' => 'Diantar Kurir',
                        'description' => 'Diantar ke alamat Anda',
                        'cost' => self::STANDARD_SHIPPING_COST,
                        'free_threshold' => self::FREE_SHIPPING_THRESHOLD
                    ]
                ]
            ]
        ]);
    }
}
