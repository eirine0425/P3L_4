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
    public function store(Request $request)
    {
        try {
            $request->validate([
                'selected_items' => 'required|string',
                'alamat_id' => 'required|exists:alamats,alamat_id',
                'payment_method' => 'required|in:bank_transfer,cod',
                'subtotal' => 'required|numeric|min:0',
                'shipping_cost' => 'required|numeric|min:0',
                'admin_fee' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0'
            ]);

            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan');
            }

            $selectedItems = json_decode($request->selected_items, true);
            
            if (empty($selectedItems)) {
                return redirect()->route('cart.index')->with('error', 'Tidak ada item yang dipilih');
            }

            // Get cart items
            $cartItems = KeranjangBelanja::with('barang')
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->whereIn('keranjang_id', $selectedItems)
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Item yang dipilih tidak ditemukan');
            }

            DB::beginTransaction();

            // Create transaction
            $transaction = Transaksi::create([
                'pembeli_id' => $pembeli->pembeli_id,
                'alamat_id' => $request->alamat_id,
                'total' => $request->total,
                'subtotal' => $request->subtotal,
                'shipping_cost' => $request->shipping_cost,
                'admin_fee' => $request->admin_fee,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'tanggal_transaksi' => now()
            ]);

            // Create transaction details
            foreach ($cartItems as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaction->transaksi_id,
                    'barang_id' => $item->barang_id,
                    'jumlah' => 1, // Assuming 1 quantity per item for second-hand goods
                    'harga' => $item->barang->harga,
                    'subtotal' => $item->barang->harga
                ]);

                // Update product status to sold
                $item->barang->update(['status' => 'terjual']);
            }

            // Remove items from cart
            KeranjangBelanja::whereIn('keranjang_id', $selectedItems)->delete();

            DB::commit();

            // Redirect to thank you page
            return redirect()->route('checkout.thank-you', ['transaction_id' => $transaction->transaksi_id])
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.')
                ->withInput();
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
                ->orderBy('tanggal_transaksi', 'desc')
                ->paginate(10);

            return view('dashboard.buyer.transactions.index', compact('transactions'));

        } catch (\Exception $e) {
            Log::error('Transaction index error: ' . $e->getMessage());
            return view('dashboard.buyer.transactions.index', ['transactions' => collect()]);
        }
    }
}