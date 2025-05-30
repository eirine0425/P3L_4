<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Barang;
use App\Models\Pembeli;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * Display a listing of ratings
     */
    public function index(Request $request)
    {
        try {
            $query = Rating::with(['pembeli', 'barang', 'transaksi']);
            
            // Filter by barang_id if provided
            if ($request->has('barang_id')) {
                $query->where('barang_id', $request->barang_id);
            }
            
            // Filter by pembeli_id if provided
            if ($request->has('pembeli_id')) {
                $query->where('pembeli_id', $request->pembeli_id);
            }
            
            // Filter by rating value if provided
            if ($request->has('rating')) {
                $query->where('rating', $request->rating);
            }
            
            // Sort by newest first
            $query->orderBy('created_at', 'desc');
            
            $ratings = $query->paginate($request->get('per_page', 15));
            
            return response()->json([
                'success' => true,
                'data' => $ratings,
                'message' => 'Ratings retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ratings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ratings'
            ], 500);
        }
    }

    /**
     * Store a newly created rating
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'barang_id' => 'required|exists:barang,barang_id',
                'transaksi_id' => 'required|exists:transaksi,transaksi_id',
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer profile not found'
                ], 404);
            }

            // Verify that the buyer actually purchased this item in this transaction
            $detailTransaksi = DetailTransaksi::whereHas('transaksi', function($query) use ($pembeli, $request) {
                $query->where('pembeli_id', $pembeli->pembeli_id)
                      ->where('transaksi_id', $request->transaksi_id)
                      ->where('status_transaksi', 'Selesai');
            })->where('barang_id', $request->barang_id)->first();

            if (!$detailTransaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only rate items you have purchased and received'
                ], 403);
            }

            // Check if already rated
            $existingRating = Rating::where('pembeli_id', $pembeli->pembeli_id)
                                  ->where('barang_id', $request->barang_id)
                                  ->first();

            if ($existingRating) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already rated this item'
                ], 409);
            }

            // Create the rating
            $rating = Rating::create([
                'pembeli_id' => $pembeli->pembeli_id,
                'barang_id' => $request->barang_id,
                'transaksi_id' => $request->transaksi_id,
                'rating' => $request->rating,
                'review' => $request->review,
            ]);

            $rating->load(['pembeli', 'barang']);

            return response()->json([
                'success' => true,
                'data' => $rating,
                'message' => 'Rating submitted successfully'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating rating: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit rating'
            ], 500);
        }
    }

    /**
     * Display the specified rating
     */
    public function show($id)
    {
        try {
            $rating = Rating::with(['pembeli', 'barang', 'transaksi'])->find($id);

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $rating,
                'message' => 'Rating retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching rating: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rating'
            ], 500);
        }
    }

    /**
     * Update the specified rating
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rating = Rating::find($id);

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating not found'
                ], 404);
            }

            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            // Verify ownership
            if ($rating->pembeli_id !== $pembeli->pembeli_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update your own ratings'
                ], 403);
            }

            $rating->update([
                'rating' => $request->rating,
                'review' => $request->review,
            ]);

            $rating->load(['pembeli', 'barang']);

            return response()->json([
                'success' => true,
                'data' => $rating,
                'message' => 'Rating updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating rating: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update rating'
            ], 500);
        }
    }

    /**
     * Remove the specified rating
     */
    public function destroy($id)
    {
        try {
            $rating = Rating::find($id);

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating not found'
                ], 404);
            }

            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            // Verify ownership
            if ($rating->pembeli_id !== $pembeli->pembeli_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own ratings'
                ], 403);
            }

            $rating->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rating deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting rating: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete rating'
            ], 500);
        }
    }

    /**
     * Get ratings for a specific item
     */
    public function getItemRatings($barangId)
    {
        try {
            $barang = Barang::find($barangId);

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            $ratings = Rating::where('barang_id', $barangId)
                           ->with(['pembeli'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

            $ratingStats = [
                'average_rating' => $barang->rating,
                'total_ratings' => $barang->total_ratings,
                'rating_distribution' => $barang->rating_distribution,
                'star_display' => $barang->star_display,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'ratings' => $ratings,
                    'stats' => $ratingStats
                ],
                'message' => 'Item ratings retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching item ratings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch item ratings'
            ], 500);
        }
    }

    /**
     * Get ratings for a specific consignor
     */
    public function getConsignorRatings($penitipId)
    {
        try {
            $penitip = \App\Models\Penitip::find($penitipId);

            if (!$penitip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consignor not found'
                ], 404);
            }

            $ratings = $penitip->ratings()
                             ->with(['pembeli', 'barang'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(10);

            $ratingStats = [
                'average_rating' => $penitip->average_rating,
                'total_ratings' => $penitip->total_ratings,
                'rating_distribution' => $penitip->rating_distribution,
                'star_display' => $penitip->star_display,
                'rating_text' => $penitip->rating_text,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'ratings' => $ratings,
                    'stats' => $ratingStats
                ],
                'message' => 'Consignor ratings retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching consignor ratings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch consignor ratings'
            ], 500);
        }
    }

    /**
     * Get buyer's own ratings
     */
    public function getMyRatings()
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer profile not found'
                ], 404);
            }

            $ratings = Rating::where('pembeli_id', $pembeli->pembeli_id)
                           ->with(['barang', 'transaksi'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $ratings,
                'message' => 'Your ratings retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user ratings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch your ratings'
            ], 500);
        }
    }

    /**
     * Get items that can be rated by the current buyer
     */
    public function getRatableItems()
    {
        try {
            $user = Auth::user();
            $pembeli = Pembeli::where('user_id', $user->id)->first();

            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer profile not found'
                ], 404);
            }

            // Get completed transactions
            $ratableItems = DetailTransaksi::whereHas('transaksi', function($query) use ($pembeli) {
                $query->where('pembeli_id', $pembeli->pembeli_id)
                      ->where('status_transaksi', 'Selesai');
            })
            ->with(['barang', 'transaksi'])
            ->whereNotExists(function($query) use ($pembeli) {
                $query->select(DB::raw(1))
                      ->from('ratings')
                      ->whereColumn('ratings.barang_id', 'detail_transaksi.barang_id')
                      ->where('ratings.pembeli_id', $pembeli->pembeli_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $ratableItems,
                'message' => 'Ratable items retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching ratable items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ratable items'
            ], 500);
        }
    }
}
