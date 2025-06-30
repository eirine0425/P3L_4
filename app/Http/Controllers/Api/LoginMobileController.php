<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Pembeli;
use App\Models\Pegawai;
use App\Models\Organisasi;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;


class LoginMobileController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->role) {
                Auth::logout();
                return response()->json(['message' => 'Akun tidak memiliki role yang valid.'], 403);
            }

            $roleName = strtolower(trim($user->role->nama_role));
            Log::info('User login (Mobile): ' . $user->email . ' with role: ' . $roleName);

            return response()->json([
                'message' => 'Login berhasil',
                'user' => $user,
                'role' => $roleName,
                'token' => $user->createToken('mobile_token')->plainTextToken
            ]);
        }

        return response()->json(['message' => 'Email atau password salah.'], 401);
    }

    public function register(Request $request)
    {
        $allowedRoles = [4, 7]; // pembeli, organisasi
        $allowedPegawaiRoles = [1, 3, 6, 8, 9, 10];

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'dob' => 'required|date',
            'phone_number' => 'nullable|string|max:20',
        ];

        if ($request->role == 'pegawai') {
            $rules['jabatan_role_id'] = 'required|integer|in:' . implode(',', $allowedPegawaiRoles);
            $rules['alamat'] = 'required|string';
        } else {
            $rules['role_id'] = 'required|integer|in:' . implode(',', $allowedRoles);
        }

        if ($request->role_id == 7 || $request->role == 'organisasi') {
            $rules['address'] = 'required|string';
            $rules['description'] = 'required|string';
        }

        $validated = $request->validate($rules);

        $roleId = $request->role_id;
        if ($request->role == 'pegawai') {
            $roleId = $request->jabatan_role_id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
            'dob' => $request->dob,
            'phone_number' => $request->phone_number ?? '',
        ]);

        if ($request->role == 'pegawai') {
            $jabatanNames = [
                1 => 'Admin',
                3 => 'Customer Service',
                6 => 'Kurir',
                8 => 'Owner',
                9 => 'Hunter',
                10 => 'Gudang'
            ];

            Pegawai::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'nama_jabatan' => $jabatanNames[$roleId] ?? 'Staff',
                'tanggal_bergabung' => now(),
                'nominal_komisi' => 0,
                'status_aktif' => 'Pending',
            ]);
        } elseif ($roleId == 4) {
            Pembeli::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'poin_loyalitas' => 0,
                'tanggal_registrasi' => now()
            ]);
        } elseif ($roleId == 7) {
            $dokumenPath = null;
            if ($request->hasFile('document')) {
                $dokumenPath = $request->file('document')->store('documents/organisasi', 'public');
            }

            Organisasi::create([
                'user_id' => $user->id,
                'nama_organisasi' => $user->name,
                'alamat' => $request->address,
                'deskripsi' => $request->description,
                'dokumen_path' => $dokumenPath
            ]);
        }

        Auth::login($user);

        Log::info('User registered (Mobile): ' . $user->email . ' with role_id: ' . $roleId);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'role' => strtolower($user->role->nama_role),
            'token' => $user->createToken('mobile_token')->plainTextToken
        ], 201);
    }

    public function logout(Request $request)
    {
        /** @var PersonalAccessToken|null $token */
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete(); // Sekarang tidak akan muncul warning
        }

        return response()->json(['message' => 'Berhasil logout']);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}
