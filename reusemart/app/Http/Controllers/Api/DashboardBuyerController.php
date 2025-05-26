<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembeli;
use App\Models\Transaksi;
use App\Models\KeranjangBelanja;
use Illuminate\Support\Facades\Log;

class DashboardBuyerController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            // Hitung total transaksi
            $totalTransactions = Transaksi::where('pembeli_id', $buyer->id)->count();
            
            // Hitung pesanan tertunda dan selesai
            $pendingOrders = Transaksi::where('pembeli_id', $buyer->id)
                ->whereIn('status', ['Menunggu Pembayaran', 'Diproses', 'Dikirim'])
                ->count();
                
            $completedOrders = Transaksi::where('pembeli_id', $buyer->id)
                ->where('status', 'Selesai')
                ->count();
                
            // Ambil poin loyalitas
            $loyaltyPoints = $buyer->poin_loyalitas ?? 0;
            
            // Ambil pesanan terbaru
            $recentOrders = Transaksi::where('pembeli_id', $buyer->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
                
            // Ambil item keranjang
            $cartItems = KeranjangBelanja::where('pembeli_id', $buyer->id)
                ->with('barang')
                ->get();
            
            return view('dashboard.buyer.index', compact(
                'totalTransactions',
                'pendingOrders',
                'completedOrders',
                'loyaltyPoints',
                'recentOrders',
                'cartItems',
                'buyer'
            ));
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in buyer dashboard: ' . $e->getMessage());
            
            // Fallback with default values
            return view('dashboard.buyer.index', [
                'totalTransactions' => 0,
                'pendingOrders' => 0,
                'completedOrders' => 0,
                'loyaltyPoints' => 0,
                'recentOrders' => collect(),
                'cartItems' => collect(),
                'buyer' => Auth::user()
            ]);
        }
    }
}
