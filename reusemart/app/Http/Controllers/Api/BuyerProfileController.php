<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembeli;
use App\Models\Transaksi;
use App\Models\Alamat;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BuyerProfileController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            // Get buyer addresses
            $alamat = Alamat::where('pembeli_id', $buyer->pembeli_id)->get();
            
            // Get recent transactions (last 5)
            $transaksi = Transaksi::where('pembeli_id', $buyer->pembeli_id)
                ->orderBy('tanggal_pesan', 'desc')
                ->take(5)
                ->get();
            
            return view('dashboard.buyer.profile.index', compact('buyer', 'alamat', 'transaksi'));
        } catch (\Exception $e) {
            Log::error('Error in buyer profile: ' . $e->getMessage());
            return redirect()->route('buyer.dashboard')->with('error', 'Terjadi kesalahan saat memuat profil.');
        }
    }
    
    public function showRewardPoints()
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return response()->json(['error' => 'Data pembeli tidak ditemukan'], 404);
            }
            
            // Calculate total points earned from transactions
            $totalPointsEarned = Transaksi::where('pembeli_id', $buyer->pembeli_id)
                ->where('status_transaksi', 'Selesai')
                ->sum('point_diperoleh');
            
            // Calculate total points used
            $totalPointsUsed = Transaksi::where('pembeli_id', $buyer->pembeli_id)
                ->sum('point_digunakan');
            
            // Get point history from transactions
            $pointHistory = Transaksi::where('pembeli_id', $buyer->pembeli_id)
                ->where(function($query) {
                    $query->where('point_diperoleh', '>', 0)
                          ->orWhere('point_digunakan', '>', 0);
                })
                ->orderBy('tanggal_pesan', 'desc')
                ->get();
            
            $rewardData = [
                'current_points' => $buyer->poin_loyalitas ?? 0,
                'total_earned' => $totalPointsEarned,
                'total_used' => $totalPointsUsed,
                'point_history' => $pointHistory
            ];
            
            return view('dashboard.buyer.profile.rewards', compact('buyer', 'rewardData'));
        } catch (\Exception $e) {
            Log::error('Error in reward points: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data poin reward.');
        }
    }
    
    public function showTransactionHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            // Get transactions with pagination and filtering
            $query = Transaksi::where('pembeli_id', $buyer->pembeli_id)
                ->with(['customerService', 'detailTransaksi.barang']);
            
            // Filter by status if provided
            if ($request->has('status') && $request->status != '') {
                $query->where('status_transaksi', $request->status);
            }
            
            // Filter by date range if provided
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('tanggal_pesan', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('tanggal_pesan', '<=', $request->date_to);
            }
            
            $transactions = $query->orderBy('tanggal_pesan', 'desc')->paginate(10);
            
            // Get transaction statistics
            $stats = [
                'total_transactions' => Transaksi::where('pembeli_id', $buyer->pembeli_id)->count(),
                'completed_transactions' => Transaksi::where('pembeli_id', $buyer->pembeli_id)
                    ->where('status_transaksi', 'Selesai')->count(),
                'pending_transactions' => Transaksi::where('pembeli_id', $buyer->pembeli_id)
                    ->whereIn('status_transaksi', ['Menunggu Pembayaran', 'Diproses', 'Dikirim'])->count(),
                'total_spent' => Transaksi::where('pembeli_id', $buyer->pembeli_id)
                    ->where('status_transaksi', 'Selesai')->sum('total_harga')
            ];
            
            return view('dashboard.buyer.profile.transaction-history', compact('buyer', 'transactions', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in transaction history: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat riwayat transaksi.');
        }
    }
    
    public function showTransactionDetail($id)
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            $transaction = Transaksi::where('transaksi_id', $id)
                ->where('pembeli_id', $buyer->pembeli_id)
                ->with(['customerService', 'detailTransaksi.barang.kategori', 'pengiriman'])
                ->first();
            
            if (!$transaction) {
                return redirect()->route('buyer.profile.transaction-history')
                    ->with('error', 'Transaksi tidak ditemukan.');
            }
            
            return view('dashboard.buyer.profile.transaction-detail', compact('buyer', 'transaction'));
        } catch (\Exception $e) {
            Log::error('Error in transaction detail: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail transaksi.');
        }
    }
    
    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'no_telepon' => 'nullable|string|max:20',
                'current_password' => 'nullable|string',
                'new_password' => 'nullable|string|min:8|confirmed',
            ]);
            
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            // Update user email
            $user->email = $request->email;
            
            // Update password if provided
            if ($request->filled('current_password') && $request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()->with('error', 'Password saat ini tidak benar.');
                }
                $user->password = Hash::make($request->new_password);
            }
            
            $user->save();
            
            // Update buyer profile
            $buyer->nama = $request->nama;
            $buyer->save();
            
            return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui profil.');
        }
    }
}
