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
        $allowedRoles = [4, 5, 7]; // pembeli, pegawai, organisasi
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|integer|in:' . implode(',', $allowedRoles),
            'dob' => 'required|date',
            'phone_number' => 'nullable|string|max:20',
        ]);

        // Validasi tambahan berdasarkan role
        if ($request->role_id == 5) { // Pegawai
            $request->validate([
                'alamat' => 'required|string',
                'gaji' => 'nullable|numeric|min:0',
            ]);
        }

        if ($request->role_id == 7) { // Organisasi
            $request->validate([
                'address' => 'required|string',
                'description' => 'required|string',
                'document' => 'required|file|mimes:pdf|max:2048',
            ]);
        }

        // Buat user baru dengan semua field yang diperlukan
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'dob' => $request->dob,
            'phone_number' => $request->phone_number ?? '',
        ]);

        // Buat record di tabel yang sesuai berdasarkan role_id
        switch ($request->role_id) {
            case 4: // Pembeli (role_id = 4)
                Pembeli::create([
                    'user_id' => $user->id,
                    'nama' => $user->name,
                    'poin_loyalitas' => 0,
                    'tanggal_registrasi' => now()
                ]);
                break;
                
            case 5: // Pegawai (role_id = 5)
                Pegawai::create([
                    'user_id' => $user->id,
                    'nama' => $user->name,
                    'alamat' => $request->alamat,
                    'gaji' => $request->gaji ?? 0,
                    'tanggal_masuk' => now()
                ]);
                break;
                
            case 7: // Organisasi (role_id = 7)
                // Handle file upload untuk dokumen
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
                break;
        }

        Auth::login($user);

        // Log untuk debugging
        Log::info('User registered: ' . $user->email . ' with role_id: ' . $request->role_id);

        // Redirect berdasarkan role_id
        switch ($request->role_id) {
            case 5: // Pegawai
                return redirect()->route('dashboard.warehouse')->with('success', 'Registrasi berhasil! Selamat datang di ReuseMart.');
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
        $roles = Role::whereIn('role_id', [4, 5, 7])->get(); // pembeli, pegawai, organisasi
        return view('auth.register', compact('roles'));
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
