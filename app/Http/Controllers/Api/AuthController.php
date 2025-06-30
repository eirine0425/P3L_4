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
use Illuminate\Validation\ValidationException;

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
   
   // ========================================
   // MOBILE APP METHODS
   // ========================================
   
   /**
    * Mobile login with JSON response
    */
   public function mobileLogin(Request $request)
   {
       try {
           Log::info('Mobile login attempt', [
               'email' => $request->email,
               'ip' => $request->ip(),
               'user_agent' => $request->userAgent()
           ]);

           // Validasi input
           $request->validate([
               'email' => 'required|email',
               'password' => 'required|string'
           ]);

           $credentials = [
               'email' => $request->email,
               'password' => $request->password
           ];

           // Coba login
           if (Auth::attempt($credentials)) {
               $user = Auth::user();
               
               Log::info('User found', ['user_id' => $user->id, 'email' => $user->email]);
               
               // Pastikan user memiliki role
               if (!$user->role) {
                   Auth::logout();
                   Log::warning('User without role attempted login', ['user_id' => $user->id]);
                   return response()->json([
                       'success' => false,
                       'message' => 'Akun tidak memiliki role yang valid.',
                       'errors' => ['email' => ['Akun tidak memiliki role yang valid.']]
                   ], 401);
               }
               
               // Create token for mobile
               $token = $user->createToken('mobile-app')->plainTextToken;
               
               // Get user role and additional data
               $roleName = strtolower(trim($user->role->nama_role));
               $userData = [
                   'id' => $user->id,
                   'name' => $user->name,
                   'email' => $user->email,
                   'role' => $roleName,
                   'phone_number' => $user->phone_number,
                   'dob' => $user->dob,
               ];
               
               // Add role-specific data
               try {
                   switch ($roleName) {
                       case 'pembeli':
                           $pembeli = Pembeli::where('user_id', $user->id)->first();
                           if ($pembeli) {
                               $userData['pembeli_id'] = $pembeli->pembeli_id;
                               $userData['poin_loyalitas'] = $pembeli->poin_loyalitas;
                           }
                           break;
                       case 'penitip':
                       case 'penjual':
                           $penitip = \App\Models\Penitip::where('user_id', $user->id)->first();
                           if ($penitip) {
                               $userData['penitip_id'] = $penitip->penitip_id;
                               $userData['total_komisi'] = $penitip->total_komisi ?? 0;
                           }
                           break;
                       case 'organisasi':
                           $organisasi = Organisasi::where('user_id', $user->id)->first();
                           if ($organisasi) {
                               $userData['organisasi_id'] = $organisasi->organisasi_id;
                               $userData['nama_organisasi'] = $organisasi->nama_organisasi;
                           }
                           break;
                       case 'pegawai':
                       case 'admin':
                       case 'cs':
                       case 'gudang':
                       case 'hunter':
                       case 'kurir':
                           $pegawai = Pegawai::where('user_id', $user->id)->first();
                           if ($pegawai) {
                               $userData['pegawai_id'] = $pegawai->pegawai_id;
                               $userData['nama_jabatan'] = $pegawai->nama_jabatan;
                               $userData['status_aktif'] = $pegawai->status_aktif;
                           }
                           break;
                   }
               } catch (\Exception $e) {
                   Log::warning('Error getting role-specific data', ['error' => $e->getMessage()]);
               }
               
               Log::info('Mobile user login successful', ['user_id' => $user->id, 'role' => $roleName]);

               return response()->json([
                   'success' => true,
                   'message' => 'Login berhasil',
                   'data' => [
                       'access_token' => $token,
                       'token_type' => 'Bearer',
                       'user' => $userData,
                       'dashboard_route' => $this->getMobileDashboardRoute($roleName)
                   ]
               ], 200);
           }

           Log::warning('Invalid credentials', ['email' => $request->email]);
           return response()->json([
               'success' => false,
               'message' => 'Email atau password salah.',
               'errors' => ['email' => ['Email atau password salah.']]
           ], 401);
           
       } catch (ValidationException $e) {
           Log::warning('Validation error in mobile login', ['errors' => $e->errors()]);
           return response()->json([
               'success' => false,
               'message' => 'Data tidak valid',
               'errors' => $e->errors()
           ], 422);
       } catch (\Exception $e) {
           Log::error('Mobile login error', [
               'error' => $e->getMessage(),
               'trace' => $e->getTraceAsString()
           ]);
           return response()->json([
               'success' => false,
               'message' => 'Terjadi kesalahan server',
               'errors' => ['server' => ['Terjadi kesalahan server: ' . $e->getMessage()]]
           ], 500);
       }
   }
   
   /**
    * Mobile register with JSON response
    */
   public function mobileRegister(Request $request)
   {
       try {
           // Validasi role yang diizinkan untuk register mandiri
           $allowedRoles = [4, 7]; // pembeli, organisasi
           
           // Validasi dasar
           $rules = [
               'name' => 'required|string|max:255',
               'email' => 'required|string|email|max:255|unique:users',
               'password' => 'required|string|min:8|confirmed',
               'dob' => 'required|date',
               'phone_number' => 'nullable|string|max:20',
               'role_id' => 'required|integer|in:' . implode(',', $allowedRoles),
           ];

           if ($request->role_id == 7) { // organisasi
               $rules['address'] = 'required|string';
               $rules['description'] = 'required|string';
           }

           $request->validate($rules);

           // Buat user baru
           $user = User::create([
               'name' => $request->name,
               'email' => $request->email,
               'password' => Hash::make($request->password),
               'role_id' => $request->role_id,
               'dob' => $request->dob,
               'phone_number' => $request->phone_number ?? '',
           ]);

           // Buat record di tabel yang sesuai berdasarkan role_id
           if ($request->role_id == 4) {
               // Pembeli
               Pembeli::create([
                   'user_id' => $user->id,
                   'nama' => $user->name,
                   'poin_loyalitas' => 0,
                   'tanggal_registrasi' => now()
               ]);
           } elseif ($request->role_id == 7) {
               // Organisasi
               Organisasi::create([
                   'user_id' => $user->id,
                   'nama_organisasi' => $user->name,
                   'alamat' => $request->address,
                   'deskripsi' => $request->description,
               ]);
           }

           // Create token
           $token = $user->createToken('mobile-app')->plainTextToken;
           
           $roleName = strtolower(trim($user->role->nama_role));
           
           Log::info('Mobile user registered: ' . $user->email . ' with role_id: ' . $request->role_id);

           return response()->json([
               'success' => true,
               'message' => 'Registrasi berhasil!',
               'data' => [
                   'access_token' => $token,
                   'token_type' => 'Bearer',
                   'user' => [
                       'id' => $user->id,
                       'name' => $user->name,
                       'email' => $user->email,
                       'role' => $roleName,
                       'phone_number' => $user->phone_number,
                       'dob' => $user->dob,
                   ],
                   'dashboard_route' => $this->getMobileDashboardRoute($roleName)
               ]
           ], 201);
           
       } catch (ValidationException $e) {
           return response()->json([
               'success' => false,
               'message' => 'Data tidak valid',
               'errors' => $e->errors()
           ], 422);
       } catch (\Exception $e) {
           Log::error('Mobile register error: ' . $e->getMessage());
           return response()->json([
               'success' => false,
               'message' => 'Terjadi kesalahan server',
               'errors' => ['server' => ['Terjadi kesalahan server']]
           ], 500);
       }
   }
   
   /**
    * Mobile logout
    */
   public function mobileLogout(Request $request)
   {
       try {
           $request->user()->tokens()->delete();
           
           return response()->json([
               'success' => true,
               'message' => 'Logout berhasil'
           ], 200);
       } catch (\Exception $e) {
           Log::error('Mobile logout error: ' . $e->getMessage());
           return response()->json([
               'success' => false,
               'message' => 'Terjadi kesalahan saat logout'
           ], 500);
       }
   }
   
   /**
    * Get mobile user data
    */
   public function mobileUser(Request $request)
   {
       try {
           $user = $request->user();
           $roleName = strtolower(trim($user->role->nama_role));
           
           $userData = [
               'id' => $user->id,
               'name' => $user->name,
               'email' => $user->email,
               'role' => $roleName,
               'phone_number' => $user->phone_number,
               'dob' => $user->dob,
           ];
           
           // Add role-specific data
           switch ($roleName) {
               case 'pembeli':
                   $pembeli = Pembeli::where('user_id', $user->id)->first();
                   if ($pembeli) {
                       $userData['pembeli_id'] = $pembeli->pembeli_id;
                       $userData['poin_loyalitas'] = $pembeli->poin_loyalitas;
                   }
                   break;
               case 'penitip':
               case 'penjual':
                   $penitip = \App\Models\Penitip::where('user_id', $user->id)->first();
                   if ($penitip) {
                       $userData['penitip_id'] = $penitip->penitip_id;
                       $userData['total_komisi'] = $penitip->total_komisi ?? 0;
                   }
                   break;
               case 'organisasi':
                   $organisasi = Organisasi::where('user_id', $user->id)->first();
                   if ($organisasi) {
                       $userData['organisasi_id'] = $organisasi->organisasi_id;
                       $userData['nama_organisasi'] = $organisasi->nama_organisasi;
                   }
                   break;
           }
           
           return response()->json([
               'success' => true,
               'data' => [
                   'user' => $userData
               ]
           ], 200);
       } catch (\Exception $e) {
           Log::error('Mobile get user error: ' . $e->getMessage());
           return response()->json([
               'success' => false,
               'message' => 'Terjadi kesalahan saat mengambil data user'
           ], 500);
       }
   }
   
   /**
    * Mobile dashboard data
    */
   public function mobileDashboard(Request $request)
   {
       try {
           $user = $request->user();
           $roleName = strtolower(trim($user->role->nama_role));
           
           $dashboardData = [
               'user' => [
                   'name' => $user->name,
                   'email' => $user->email,
                   'role' => $roleName
               ],
               'quick_actions' => $this->getMobileQuickActions($roleName),
               'stats' => $this->getMobileStats($user, $roleName)
           ];
           
           return response()->json([
               'success' => true,
               'data' => $dashboardData
           ], 200);
       } catch (\Exception $e) {
           Log::error('Mobile dashboard error: ' . $e->getMessage());
           return response()->json([
               'success' => false,
               'message' => 'Terjadi kesalahan saat mengambil data dashboard'
           ], 500);
       }
   }
   
   /**
    * Refresh token
    */
   public function refreshToken(Request $request)
   {
       try {
           $user = $request->user();
           
           // Delete current token
           $request->user()->tokens()->delete();
           
           // Create new token
           $token = $user->createToken('mobile-app')->plainTextToken;
           
           return response()->json([
               'success' => true,
               'data' => [
                   'access_token' => $token,
                   'token_type' => 'Bearer'
               ]
           ], 200);
       } catch (\Exception $e) {
           Log::error('Mobile refresh token error: ' . $e->getMessage());
           return response()->json([
               'success' => false,
               'message' => 'Terjadi kesalahan saat refresh token'
           ], 500);
       }
   }
   
   /**
    * Get mobile dashboard route based on role
    */
   private function getMobileDashboardRoute($roleName)
   {
       return match ($roleName) {
           'owner' => '/mobile/owner/dashboard',
           'admin' => '/mobile/admin/dashboard',
           'pegawai', 'gudang', 'pegawai gudang' => '/mobile/warehouse/dashboard',
           'cs' => '/mobile/cs/dashboard',
           'penitip', 'penjual' => '/mobile/consignor/dashboard',
           'organisasi' => '/mobile/organization/dashboard',
           'pembeli' => '/mobile/buyer/dashboard',
           'hunter' => '/mobile/hunter/dashboard',
           default => '/mobile/buyer/dashboard'
       };
   }
   
   /**
    * Get mobile quick actions based on role
    */
   private function getMobileQuickActions($roleName)
   {
       return match ($roleName) {
           'pembeli' => [
               ['title' => 'Lihat Produk', 'route' => '/products', 'icon' => 'shopping_bag'],
               ['title' => 'Keranjang', 'route' => '/cart', 'icon' => 'shopping_cart'],
               ['title' => 'Riwayat Transaksi', 'route' => '/transactions', 'icon' => 'history'],
               ['title' => 'Profil', 'route' => '/profile', 'icon' => 'person']
           ],
           'penitip', 'penjual' => [
               ['title' => 'Barang Saya', 'route' => '/my-items', 'icon' => 'inventory'],
               ['title' => 'Transaksi', 'route' => '/transactions', 'icon' => 'receipt'],
               ['title' => 'Penjemputan', 'route' => '/pickup', 'icon' => 'local_shipping'],
               ['title' => 'Rating', 'route' => '/ratings', 'icon' => 'star']
           ],
           'admin' => [
               ['title' => 'Kelola User', 'route' => '/users', 'icon' => 'people'],
               ['title' => 'Kelola Penitip', 'route' => '/consignors', 'icon' => 'store'],
               ['title' => 'Laporan', 'route' => '/reports', 'icon' => 'assessment'],
               ['title' => 'Rating', 'route' => '/ratings', 'icon' => 'star']
           ],
           default => [
               ['title' => 'Dashboard', 'route' => '/dashboard', 'icon' => 'dashboard'],
               ['title' => 'Profil', 'route' => '/profile', 'icon' => 'person']
           ]
       };
   }
   
   /**
    * Get mobile stats based on role
    */
   private function getMobileStats($user, $roleName)
   {
       try {
           switch ($roleName) {
               case 'pembeli':
                   $pembeli = Pembeli::where('user_id', $user->id)->first();
                   if ($pembeli) {
                       $cartCount = \App\Models\KeranjangBelanja::where('pembeli_id', $pembeli->pembeli_id)->count();
                       $transactionCount = \App\Models\Transaksi::where('pembeli_id', $pembeli->pembeli_id)->count();
                       return [
                           'cart_items' => $cartCount,
                           'total_transactions' => $transactionCount,
                           'loyalty_points' => $pembeli->poin_loyalitas ?? 0
                       ];
                   }
                   break;
                   
               case 'penitip':
               case 'penjual':
                   $penitip = \App\Models\Penitip::where('user_id', $user->id)->first();
                   if ($penitip) {
                       $itemCount = \App\Models\Barang::where('penitip_id', $penitip->penitip_id)->count();
                       $soldCount = \App\Models\Barang::where('penitip_id', $penitip->penitip_id)
                           ->where('status_barang', 'Terjual')->count();
                       return [
                           'total_items' => $itemCount,
                           'sold_items' => $soldCount,
                           'total_commission' => $penitip->total_komisi ?? 0
                       ];
                   }
                   break;
                   
               default:
                   return [];
           }
           
           return [];
       } catch (\Exception $e) {
           Log::error('Mobile stats error: ' . $e->getMessage());
           return [];
       }
   }
}