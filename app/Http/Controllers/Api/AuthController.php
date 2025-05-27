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

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Pastikan user memiliki role
            if (!$user->role) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun tidak memiliki role yang valid.',
                ]);
            }
            
            $roleName = strtolower(trim($user->role->nama_role));
            
            // Log untuk debugging
            Log::info('User login: ' . $user->email . ' with role: ' . $roleName);

            switch ($roleName) {
                case 'owner':
                    return redirect()->route('dashboard.owner');
                case 'admin':
                    return redirect()->route('dashboard.admin');
                case 'pegawai':
                case 'gudang':
                    return redirect()->route('dashboard.warehouse');
                case 'cs':
                    return redirect()->route('dashboard.cs');
                case 'penjual':
                    return redirect()->route('dashboard.consignor');
                case 'organisasi':
                    return redirect()->route('dashboard.organization');
                case 'pembeli':
                    return redirect()->route('dashboard.buyer');
                case 'kurir':
                    return redirect()->route('dashboard.warehouse'); // Kurir menggunakan dashboard warehouse
                case 'hunter':
                    return redirect()->route('dashboard.warehouse'); // Hunter menggunakan dashboard warehouse
                default:
                    // Fallback ke dashboard buyer jika role tidak dikenali
                    Log::warning('Unrecognized role: ' . $roleName . ' for user: ' . $user->email);
                    return redirect()->route('dashboard.buyer');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function register(Request $request)
    {
        // Validasi role yang diizinkan untuk register mandiri
        $allowedRoles = [4, 7]; // pembeli, organisasi
        $allowedPegawaiRoles = [1, 3, 6, 8, 9, 10]; // admin, cs, kurir, owner, hunter, gudang
        
        // Validasi dasar
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'dob' => 'required|date',
            'phone_number' => 'nullable|string|max:20',
        ];

        // Validasi berdasarkan role
        if ($request->role == 'pegawai') {
            $rules['jabatan_role_id'] = 'required|integer|in:' . implode(',', $allowedPegawaiRoles);
            $rules['alamat'] = 'required|string';
            $rules['gaji_harapan'] = 'nullable|numeric|min:0';
            $rules['pengalaman'] = 'nullable|string';
        } else {
            $rules['role_id'] = 'required|integer|in:' . implode(',', $allowedRoles);
        }

        if ($request->role_id == 7 || $request->role == 'organisasi') {
            $rules['address'] = 'required|string';
            $rules['description'] = 'required|string';

        }

        $request->validate($rules);

        // Tentukan role_id untuk pegawai
        $roleId = $request->role_id;
        if ($request->role == 'pegawai') {
            $roleId = $request->jabatan_role_id;
        }

        // Buat user baru dengan semua field yang diperlukan
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
            'dob' => $request->dob,
            'phone_number' => $request->phone_number ?? '',
        ]);

        // Buat record di tabel yang sesuai berdasarkan role_id
        if ($request->role == 'pegawai') {
            // Untuk semua jenis pegawai (admin, cs, kurir, owner, hunter, gudang)
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
                'status_aktif' => 'Pending', // Status pending sampai diverifikasi admin
            ]);
        } elseif ($roleId == 4) {
            // Pembeli
            Pembeli::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'poin_loyalitas' => 0,
                'tanggal_registrasi' => now()
            ]);
        } elseif ($roleId == 7) {
            // Organisasi
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

        // Log untuk debugging
        Log::info('User registered: ' . $user->email . ' with role_id: ' . $roleId);

        // Redirect berdasarkan role_id
        switch ($roleId) {
            case 1: // Admin
                return redirect()->route('dashboard.admin')->with('success', 'Registrasi berhasil! Akun admin Anda sedang dalam proses verifikasi.');
            case 3: // CS
                return redirect()->route('dashboard.cs')->with('success', 'Registrasi berhasil! Akun CS Anda sedang dalam proses verifikasi.');
            case 6: // Kurir
            case 9: // Hunter
            case 10: // Gudang
                return redirect()->route('dashboard.warehouse')->with('success', 'Registrasi berhasil! Akun pegawai Anda sedang dalam proses verifikasi.');
            case 8: // Owner
                return redirect()->route('dashboard.owner')->with('success', 'Registrasi berhasil! Akun owner Anda sedang dalam proses verifikasi.');
            case 7: // Organisasi
                return redirect()->route('dashboard.organization')->with('success', 'Registrasi berhasil! Akun organisasi Anda sedang dalam proses verifikasi.');
            case 4: // Pembeli
            default:
                return redirect()->route('dashboard.buyer')->with('success', 'Registrasi berhasil! Selamat berbelanja di ReuseMart.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    // Get current user
    public function me(Request $request)
    {
        $user = Auth::user();
        
        if ($request->expectsJson()) {
            return response()->json(['user' => $user]);
        }
        
        return view('profile', compact('user'));
    }
    
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    // Tampilkan form register
    public function showRegisterForm()
    {
        // Hanya tampilkan role yang diizinkan untuk register mandiri
        $roles = Role::whereIn('role_id', [4, 7])->get(); // pembeli, organisasi
        $pegawaiRoles = Role::whereIn('role_id', [1, 3, 6, 8, 9, 10])->get(); // jabatan pegawai
        return view('auth.register', compact('roles', 'pegawaiRoles'));
    }
    
    // Add this method as a fallback
    public function showRegistrationForm()
    {
        return $this->showRegisterForm();
    }
    
    // Tampilkan form reset password
    public function showResetForm()
    {
        return view('auth.passwords.email');
    }
    
    // Tampilkan form reset password dengan token
    public function showResetPasswordForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }
}
