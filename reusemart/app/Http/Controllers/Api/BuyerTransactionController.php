<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembeli;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Log;

class BuyerTransactionController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            // Ambil semua transaksi pembeli dengan pagination
            $transactions = Transaksi::where('pembeli_id', $buyer->id)
                ->with(['detailTransaksi.barang'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('dashboard.buyer.transactions.index', compact('transactions', 'buyer'));
        } catch (\Exception $e) {
            Log::error('Error in buyer transactions: ' . $e->getMessage());
            return redirect()->route('dashboard.buyer')->with('error', 'Terjadi kesalahan saat mengambil data transaksi.');
        }
    }
    
    public function show($id)
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            // Ambil detail transaksi
            $transaction = Transaksi::where('id', $id)
                ->where('pembeli_id', $buyer->id)
                ->with(['detailTransaksi.barang', 'pengiriman'])
                ->first();
                
            if (!$transaction) {
                return redirect()->route('buyer.transactions')->with('error', 'Transaksi tidak ditemukan.');
            }
            
            return view('dashboard.buyer.transactions.show', compact('transaction', 'buyer'));
        } catch (\Exception $e) {
            Log::error('Error in buyer transaction detail: ' . $e->getMessage());
            return redirect()->route('buyer.transactions')->with('error', 'Terjadi kesalahan saat mengambil detail transaksi.');
        }
    }
}
