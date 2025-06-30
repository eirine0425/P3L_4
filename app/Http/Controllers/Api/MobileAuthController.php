<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pembeli;
use App\Models\Penitip;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('mobile-app')->plainTextToken;

            // Load user relationships based on role
            $userData = $user->load(['role']);
            
            if ($user->role === 'pembeli') {
                $userData->load('pembeli');
            } elseif ($user->role === 'penitip') {
                $userData->load('penitip');
            }

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $userData,
                    'token' => $token,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah',
        ], 401);
    }

    public function logout(Request $request)
    {
        // Revoke the current access token
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();
        
        // Alternative method - revoke all tokens for the user
        // $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        // Load user relationships based on role
        if ($user->role === 'pembeli') {
            $user->load('pembeli');
        } elseif ($user->role === 'penitip') {
            $user->load('penitip');
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        $dashboardData = [
            'user' => $user,
            'role' => $user->role,
        ];

        // Add role-specific data
        if ($user->role === 'pembeli') {
            $pembeli = $user->pembeli;
            if ($pembeli) {
                $dashboardData['pembeli_data'] = $pembeli;
                $dashboardData['total_transactions'] = $pembeli->transaksis()->count();
                $dashboardData['loyalty_points'] = $pembeli->poin_loyalitas ?? 0;
            }
        } elseif ($user->role === 'penitip') {
            $penitip = $user->penitip;
            if ($penitip) {
                $dashboardData['penitip_data'] = $penitip;
                $dashboardData['total_items'] = $penitip->barangs()->count();
                $dashboardData['commission_balance'] = $penitip->saldo_komisi ?? 0;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $dashboardData,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:pembeli,penitip',
            'phone' => 'required|string|max:20',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
            ]);

            // Create role-specific record
            if ($request->role === 'pembeli') {
                Pembeli::create([
                    'user_id' => $user->id,
                    'poin_loyalitas' => 0,
                ]);
            } elseif ($request->role === 'penitip') {
                Penitip::create([
                    'user_id' => $user->id,
                    'saldo_komisi' => 0,
                ]);
            }

            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();
        
        // Create new token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil diperbarui',
            'data' => [
                'token' => $token,
            ]
        ]);
    }
}