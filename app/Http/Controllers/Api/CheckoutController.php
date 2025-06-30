<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\KeranjangBelanja;
use App\Models\Alamat;
use App\Models\Pembeli;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Ambil dari session dengan validasi yang lebih baik
        $rawSelected = session('checkout_items', []);
        $selectedIds = [];

        // Validasi dan konversi data session
        if (is_string($rawSelected)) {
            $decoded = json_decode($rawSelected, true);
            $selectedIds = is_array($decoded) ? $decoded : [];
        } elseif (is_array($rawSelected)) {
            $selectedIds = $rawSelected;
        }

        // Jika tidak ada di session, coba ambil dari request
        if (empty($selectedIds)) {
            $requestItems = $request->input('selected_items', []);
            if (is_string($requestItems)) {
                $decoded = json_decode($requestItems, true);
                $selectedIds = is_array($decoded) ? $decoded : [];
            } elseif (is_array($requestItems)) {
                $selectedIds = $requestItems;
            }
        }

        // Pastikan $selectedIds adalah array dan tidak kosong
        $selectedIds = is_array($selectedIds) ? array_filter($selectedIds) : [];

        // Log untuk debugging
        Log::info('Checkout page loaded', [
            'selected_ids' => $selectedIds,
            'user_id' => Auth::id()
        ]);

        // Ambil data keranjang beserta barang hanya jika ada selectedIds
        $selectedItems = collect(); // Inisialisasi sebagai empty collection
        $subtotal = 0;
        $itemsLoadedFromServer = false;

        // Ambil alamat user yang sedang login
        $alamats = collect();
        $alamatTerpilih = null;
        if (Auth::check()) {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            if ($pembeli) {
                $alamats = Alamat::where('pembeli_id', $pembeli->pembeli_id)
                    ->orderBy('status_default', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
                $defaultAlamat = $alamats->where('status_default', 'Y')->first();
                $alamatTerpilih = $defaultAlamat ? $defaultAlamat->alamat_id : null;
            }
        }

        if (!empty($selectedIds) && is_array($selectedIds)) {
            try {
                $selectedItems = KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                    ->whereIn('keranjang_id', $selectedIds)
                    ->get();
                // Hitung subtotal hanya jika ada items
                if ($selectedItems->isNotEmpty()) {
                    foreach($selectedItems as $item) {
                        $barang = $item->barang ?? null;
                        if ($barang) {
                            $harga = $barang->harga ?? 0;
                            $jumlah = $item->jumlah ?? 1;
                            $subtotal += $harga * $jumlah;
                        }
                    }
                    $itemsLoadedFromServer = true;
                }
            } catch (\Exception $e) {
                // Jika terjadi error saat query, set sebagai empty collection
                $selectedItems = collect();
                $subtotal = 0;
                $itemsLoadedFromServer = false;
                // Log error untuk debugging
                Log::error('Error loading selected items: ' . $e->getMessage());
            }
        }

        // Hitung shipping cost - default gratis untuk ambil sendiri
        $shippingCost = 0;
        $total = $subtotal + $shippingCost;

        return view('checkout.index', compact(
            'selectedIds', 
            'selectedItems', 
            'subtotal', 
            'shippingCost', 
            'total', 
            'alamats', 
            'alamatTerpilih', 
            'itemsLoadedFromServer'
        ));
    }
}
