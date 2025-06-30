<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembeli;
use App\Models\Rating;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\Alamat;
use App\Models\User;
use App\Models\Barang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BuyerProfileController extends Controller
{
    /**
     * Display buyer profile index page
     */
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
    
    /**
     * Show reward points page
     */
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
    
    /**
     * Show transaction history with filtering
     */
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
    
    /**
     * Show specific transaction detail
     */
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
    
    /**
     * Update buyer profile information
     */
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

    /**
     * Show ratings and reviews page
     */
    public function showRatings()
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->route('home')->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            // Get buyer's ratings
            $ratings = Rating::where('pembeli_id', $buyer->pembeli_id)
                            ->with(['barang', 'transaksi'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
            
            // Get items that can be rated
            $ratableItems = DetailTransaksi::whereHas('transaksi', function($query) use ($buyer) {
                $query->where('pembeli_id', $buyer->pembeli_id)
                      ->where('status_transaksi', 'Selesai');
            })
            ->with(['barang', 'transaksi'])
            ->whereNotExists(function($query) use ($buyer) {
                $query->select(DB::raw(1))
                      ->from('ratings')
                      ->whereColumn('ratings.barang_id', 'detail_transaksi.barang_id')
                      ->where('ratings.pembeli_id', $buyer->pembeli_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
            return view('dashboard.buyer.profile.ratings', compact('buyer', 'ratings', 'ratableItems'));
        } catch (\Exception $e) {
            Log::error('Error in buyer ratings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data rating.');
        }
    }

    /**
     * Submit a new rating for a purchased item
     */
    public function submitRating(Request $request)
    {
        try {
            $request->validate([
                'barang_id' => 'required|exists:barang,barang_id',
                'transaksi_id' => 'required|exists:transaksi,transaksi_id',
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);
            
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            // Verify that the buyer actually purchased this item
            $detailTransaksi = DetailTransaksi::whereHas('transaksi', function($query) use ($buyer, $request) {
                $query->where('pembeli_id', $buyer->pembeli_id)
                      ->where('transaksi_id', $request->transaksi_id)
                      ->where('status_transaksi', 'Selesai');
            })->where('barang_id', $request->barang_id)->first();
            
            if (!$detailTransaksi) {
                return redirect()->back()->with('error', 'Anda hanya dapat memberikan rating untuk barang yang telah dibeli dan diterima.');
            }
            
            // Check if already rated
            $existingRating = Rating::where('pembeli_id', $buyer->pembeli_id)
                                  ->where('barang_id', $request->barang_id)
                                  ->first();
            
            if ($existingRating) {
                return redirect()->back()->with('error', 'Anda sudah memberikan rating untuk barang ini.');
            }
            
            // Create the rating
            $rating = Rating::create([
                'pembeli_id' => $buyer->pembeli_id,
                'barang_id' => $request->barang_id,
                'transaksi_id' => $request->transaksi_id,
                'rating' => $request->rating,
                'review' => $request->review,
            ]);
            
            // Update the average rating in barang table if needed
            $this->updateBarangRating($request->barang_id);
            
            return redirect()->back()->with('success', 'Rating berhasil diberikan.');
        } catch (\Exception $e) {
            Log::error('Error submitting rating: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memberikan rating.');
        }
    }

    /**
     * Update an existing rating
     */
    public function updateRating(Request $request, $ratingId)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);
            
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            $rating = Rating::where('rating_id', $ratingId)
                           ->where('pembeli_id', $buyer->pembeli_id)
                           ->first();
            
            if (!$rating) {
                return redirect()->back()->with('error', 'Rating tidak ditemukan atau bukan milik Anda.');
            }
            
            $rating->update([
                'rating' => $request->rating,
                'review' => $request->review,
            ]);
            
            // Update the average rating in barang table
            $this->updateBarangRating($rating->barang_id);
            
            return redirect()->back()->with('success', 'Rating berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating rating: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui rating.');
        }
    }

    /**
     * Delete a rating
     */
    public function deleteRating($ratingId)
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return redirect()->back()->with('error', 'Data pembeli tidak ditemukan.');
            }
            
            $rating = Rating::where('rating_id', $ratingId)
                           ->where('pembeli_id', $buyer->pembeli_id)
                           ->first();
            
            if (!$rating) {
                return redirect()->back()->with('error', 'Rating tidak ditemukan atau bukan milik Anda.');
            }
            
            $barangId = $rating->barang_id;
            $rating->delete();
            
            // Update the average rating in barang table
            $this->updateBarangRating($barangId);
            
            return redirect()->back()->with('success', 'Rating berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting rating: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus rating.');
        }
    }

    /**
     * Get buyer's rating statistics
     */
    public function getRatingStats()
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return response()->json(['error' => 'Data pembeli tidak ditemukan'], 404);
            }
            
            $stats = [
                'total_ratings' => Rating::where('pembeli_id', $buyer->pembeli_id)->count(),
                'average_rating_given' => Rating::where('pembeli_id', $buyer->pembeli_id)->avg('rating'),
                'rating_distribution' => Rating::where('pembeli_id', $buyer->pembeli_id)
                    ->select('rating', DB::raw('count(*) as count'))
                    ->groupBy('rating')
                    ->pluck('count', 'rating')
                    ->toArray(),
                'recent_ratings' => Rating::where('pembeli_id', $buyer->pembeli_id)
                    ->with(['barang', 'transaksi'])
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(),
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error getting rating stats: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat statistik rating'], 500);
        }
    }

    /**
     * Get items that can be rated by the buyer
     */
    public function getRatableItems()
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->first();
            
            if (!$buyer) {
                return response()->json(['error' => 'Data pembeli tidak ditemukan'], 404);
            }
            
            $ratableItems = DetailTransaksi::whereHas('transaksi', function($query) use ($buyer) {
                $query->where('pembeli_id', $buyer->pembeli_id)
                      ->where('status_transaksi', 'Selesai');
            })
            ->with(['barang', 'transaksi'])
            ->whereNotExists(function($query) use ($buyer) {
                $query->select(DB::raw(1))
                      ->from('ratings')
                      ->whereColumn('ratings.barang_id', 'detail_transaksi.barang_id')
                      ->where('ratings.pembeli_id', $buyer->pembeli_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
            return response()->json($ratableItems);
        } catch (\Exception $e) {
            Log::error('Error getting ratable items: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data barang'], 500);
        }
    }

    /**
     * Private method to update average rating in barang table
     */
    private function updateBarangRating($barangId)
    {
        try {
            $averageRating = Rating::where('barang_id', $barangId)->avg('rating');
            
            Barang::where('barang_id', $barangId)->update([
                'rating' => $averageRating ? round($averageRating, 2) : 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating barang rating: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get buyer profile data
     */
    public function getProfile()
    {
        try {
            $user = Auth::user();
            $buyer = Pembeli::where('user_id', $user->id)->with(['alamat'])->first();
            
            if (!$buyer) {
                return response()->json(['error' => 'Data pembeli tidak ditemukan'], 404);
            }
            
            return response()->json([
                'buyer' => $buyer,
                'user' => $user,
                'stats' => [
                    'total_transactions' => Transaksi::where('pembeli_id', $buyer->pembeli_id)->count(),
                    'total_ratings' => Rating::where('pembeli_id', $buyer->pembeli_id)->count(),
                    'loyalty_points' => $buyer->poin_loyalitas ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting profile data: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data profil'], 500);
        }
    }
}
