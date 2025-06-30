<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pembeli;
use App\Models\Transaksi;
use App\Models\Alamat;
use Illuminate\Support\Facades\Hash;

class MobileBuyerController extends Controller
{
    public function dashboard()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get buyer profile
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer profile not found'
                ], 404);
            }

            // Get transaction stats
            $totalTransactions = Transaksi::where('pembeli_id', $pembeli->id)->count();
            $completedTransactions = Transaksi::where('pembeli_id', $pembeli->id)
                ->where('status_transaksi', 'selesai')->count();
            $totalSpent = Transaksi::where('pembeli_id', $pembeli->id)
                ->where('status_transaksi', 'selesai')
                ->sum('total_harga');

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'user' => $user,
                    'pembeli' => $pembeli,
                    'stats' => [
                        'total_transactions' => $totalTransactions,
                        'completed_transactions' => $completedTransactions,
                        'total_spent' => $totalSpent
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function profile()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get buyer profile
            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer profile not found'
                ], 404);
            }

            // Get addresses
            $addresses = Alamat::where('user_id', $user->id)->get();

            // Get transaction stats
            $totalTransactions = Transaksi::where('pembeli_id', $pembeli->id)->count();
            $completedTransactions = Transaksi::where('pembeli_id', $pembeli->id)
                ->where('status_transaksi', 'selesai')->count();
            $totalSpent = Transaksi::where('pembeli_id', $pembeli->id)
                ->where('status_transaksi', 'selesai')
                ->sum('total_harga');

            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => [
                    'user' => $user,
                    'pembeli' => $pembeli,
                    'addresses' => $addresses,
                    'stats' => [
                        'total_transactions' => $totalTransactions,
                        'completed_transactions' => $completedTransactions,
                        'total_spent' => $totalSpent
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'current_password' => 'nullable|string',
                'new_password' => 'nullable|string|min:6|confirmed',
            ]);

            // Update user data
            $user->name = $request->name;
            if ($request->phone_number) {
                $user->phone_number = $request->phone_number;
            }

            // Update password if provided
            if ($request->current_password && $request->new_password) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect'
                    ], 400);
                }
                $user->password = Hash::make($request->new_password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $user
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rewardPoints()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer profile not found'
                ], 404);
            }

            // Get point history from transactions
            $pointHistory = Transaksi::where('pembeli_id', $pembeli->id)
                ->where(function($query) {
                    $query->where('point_diperoleh', '>', 0)
                          ->orWhere('point_digunakan', '>', 0);
                })
                ->orderBy('tanggal_pesan', 'desc')
                ->get()
                ->map(function($transaction) {
                    return [
                        'transaksi_id' => $transaction->id,
                        'tanggal' => $transaction->tanggal_pesan,
                        'points_earned' => $transaction->point_diperoleh,
                        'points_used' => $transaction->point_digunakan,
                    ];
                });

            $totalEarned = Transaksi::where('pembeli_id', $pembeli->id)->sum('point_diperoleh');
            $totalUsed = Transaksi::where('pembeli_id', $pembeli->id)->sum('point_digunakan');

            return response()->json([
                'success' => true,
                'message' => 'Reward points retrieved successfully',
                'data' => [
                    'current_points' => $pembeli->poin_loyalitas,
                    'total_earned' => $totalEarned,
                    'total_used' => $totalUsed,
                    'point_history' => $pointHistory
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving reward points: ' . $e->getMessage()
            ], 500);
        }
    }

    public function transactions(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer profile not found'
                ], 404);
            }

            $perPage = $request->get('per_page', 10);
            $status = $request->get('status');

            $query = Transaksi::where('pembeli_id', $pembeli->id)
                ->with(['detailTransaksi.barang']);

            if ($status) {
                $query->where('status_transaksi', $status);
            }

            $transactions = $query->orderBy('tanggal_pesan', 'desc')
                ->paginate($perPage);

            // Transform the data to include items
            $transformedTransactions = $transactions->getCollection()->map(function($transaction) {
                return [
                    'transaksi_id' => $transaction->id,
                    'pembeli_id' => $transaction->pembeli_id,
                    'status_transaksi' => $transaction->status_transaksi,
                    'total_harga' => $transaction->total_harga,
                    'tanggal_pesan' => $transaction->tanggal_pesan,
                    'tanggal_pelunasan' => $transaction->tanggal_pelunasan,
                    'metode_pengiriman' => $transaction->metode_pengiriman ?? 'pickup',
                    'point_diperoleh' => $transaction->point_diperoleh,
                    'point_digunakan' => $transaction->point_digunakan,
                    'items' => $transaction->detailTransaksi->map(function($detail) {
                        return [
                            'id' => $detail->id,
                            'barang_id' => $detail->barang_id,
                            'nama_barang' => $detail->barang->nama_barang ?? 'Unknown',
                            'jumlah' => $detail->jumlah,
                            'harga_satuan' => $detail->harga_satuan,
                            'subtotal' => $detail->subtotal,
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Transactions retrieved successfully',
                'data' => [
                    'data' => $transformedTransactions,
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function transactionDetail($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $pembeli = Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer profile not found'
                ], 404);
            }

            $transaction = Transaksi::where('id', $id)
                ->where('pembeli_id', $pembeli->id)
                ->with(['detailTransaksi.barang'])
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            $transformedTransaction = [
                'transaksi_id' => $transaction->id,
                'pembeli_id' => $transaction->pembeli_id,
                'status_transaksi' => $transaction->status_transaksi,
                'total_harga' => $transaction->total_harga,
                'tanggal_pesan' => $transaction->tanggal_pesan,
                'tanggal_pelunasan' => $transaction->tanggal_pelunasan,
                'metode_pengiriman' => $transaction->metode_pengiriman ?? 'pickup',
                'point_diperoleh' => $transaction->point_diperoleh,
                'point_digunakan' => $transaction->point_digunakan,
                'items' => $transaction->detailTransaksi->map(function($detail) {
                    return [
                        'id' => $detail->id,
                        'barang_id' => $detail->barang_id,
                        'nama_barang' => $detail->barang->nama_barang ?? 'Unknown',
                        'jumlah' => $detail->jumlah,
                        'harga_satuan' => $detail->harga_satuan,
                        'subtotal' => $detail->subtotal,
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Transaction detail retrieved successfully',
                'data' => $transformedTransaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving transaction detail: ' . $e->getMessage()
            ], 500);
        }
    }
}
