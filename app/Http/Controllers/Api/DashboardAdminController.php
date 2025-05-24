<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Penitip;
use App\Models\Pegawai;
use App\Models\Organisasi;
use App\UseCases\Penitip\PenitipUseCase;
use App\DTOs\Penitip\CreatePenitipRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DashboardAdminController extends Controller
{
    public function __construct(
        protected PenitipUseCase $penitipUseCase
    ) {}

    public function index()
    {
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalEmployees = Pegawai::count();
        $totalOrganizations = Organisasi::count();
        $totalPenitips = Penitip::count();
        $recentUsers = User::with('role')->latest()->take(5)->get();

        return view('dashboard.admin.index', compact(
            'totalUsers',
            'totalRoles', 
            'totalEmployees',
            'totalOrganizations',
            'totalPenitips',
            'recentUsers'
        ));
    }

    public function users()
    {
        $users = User::with('role')->paginate(10);
        return view('dashboard.admin.users.index', compact('users'));
    }

    public function penitips()
    {
        $penitips = Penitip::with('user')->paginate(10);
        return view('dashboard.admin.penitips.index', compact('penitips'));
    }

    public function createPenitip()
    {
        return view('dashboard.admin.penitips.create');
    }

    public function storePenitip(Request $request)
    {
        // Debug: Log request data
        Log::info('Penitip registration attempt', $request->all());

        // Validasi sesuai dengan struktur database
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'dob' => 'required|date|before:today',
            'phone_number' => 'required|string|max:15|regex:/^[0-9+\-\s()]+$/',
            'nama_penitip' => 'required|string|max:255',
            'no_ktp' => 'required|string|size:16|unique:penitip,no_ktp',
            'tanggal_registrasi' => 'required|date',
            'badge' => 'nullable|string|max:10',
            'periode' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Cari role penitip dengan benar
            $penitipRole = Role::where('nama_role', 'penitip')
                ->orWhere('nama_role', 'Penitip')
                ->orWhere('role_id', 2)
                ->first();

            if (!$penitipRole) {
                throw new \Exception('Role penitip tidak ditemukan');
            }

            Log::info('Found penitip role', ['role' => $penitipRole->toArray()]);

            // Create user account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'dob' => $validated['dob'],
                'phone_number' => $validated['phone_number'],
                'role_id' => $penitipRole->role_id,
                'email_verified_at' => now()
            ]);

            Log::info('User created', ['user_id' => $user->id]);

            // Create penitip record sesuai struktur database
            $penitip = Penitip::create([
                'nama' => $validated['nama_penitip'],
                'point_donasi' => 0,
                'tanggal_registrasi' => $validated['tanggal_registrasi'],
                'no_ktp' => $validated['no_ktp'],
                'user_id' => $user->id,
                'badge' => $validated['badge'] ?? 'no',
                'periode' => $validated['periode'],
                'saldo' => 0
            ]);

            Log::info('Penitip created', ['penitip_id' => $penitip->penitip_id]);

            DB::commit();

            return redirect()->route('dashboard.admin.penitips')
                ->with('success', 'Penitip berhasil didaftarkan!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Penitip registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Gagal mendaftarkan penitip: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function showPenitip($id)
    {
        $penitip = Penitip::with('user')->find($id);
        if (!$penitip) {
            return redirect()->route('dashboard.admin.penitips')
                ->with('error', 'Penitip tidak ditemukan!');
        }
        return view('dashboard.admin.penitips.show', compact('penitip'));
    }

    public function editPenitip($id)
    {
        $penitip = Penitip::with('user')->find($id);
        if (!$penitip) {
            return redirect()->route('dashboard.admin.penitips')
                ->with('error', 'Penitip tidak ditemukan!');
        }
        return view('dashboard.admin.penitips.edit', compact('penitip'));
    }

    public function updatePenitip(Request $request, $id)
    {
        $penitip = Penitip::with('user')->find($id);
        if (!$penitip) {
            return redirect()->route('dashboard.admin.penitips')
                ->with('error', 'Penitip tidak ditemukan!');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $penitip->user_id,
            'phone_number' => 'required|string|max:15|regex:/^[0-9+\-\s()]+$/',
            'nama_penitip' => 'required|string|max:255',
            'no_ktp' => 'required|string|size:16|unique:penitip,no_ktp,' . $id . ',penitip_id',
            'tanggal_registrasi' => 'required|date',
            'badge' => 'nullable|string|max:10',
            'periode' => 'nullable|string|max:50',
            'point_donasi' => 'nullable|integer|min:0',
            'saldo' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update user account
            $penitip->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
            ]);

            // Update penitip record
            $penitip->update([
                'nama' => $validated['nama_penitip'],
                'tanggal_registrasi' => $validated['tanggal_registrasi'],
                'no_ktp' => $validated['no_ktp'],
                'badge' => $validated['badge'] ?? 'no',
                'periode' => $validated['periode'],
                'point_donasi' => $validated['point_donasi'] ?? $penitip->point_donasi,
                'saldo' => $validated['saldo'] ?? $penitip->saldo,
            ]);

            DB::commit();

            return redirect()->route('dashboard.admin.penitips')
                ->with('success', 'Data penitip berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Penitip update failed', [
                'error' => $e->getMessage(),
                'penitip_id' => $id
            ]);
            
            return back()->withErrors(['error' => 'Gagal memperbarui data penitip: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroyPenitip($id)
    {
        DB::beginTransaction();
        try {
            $penitip = Penitip::with('user')->find($id);
            
            if (!$penitip) {
                return back()->withErrors(['error' => 'Penitip tidak ditemukan!']);
            }

            // Delete user account (will cascade delete penitip if foreign key is set)
            $penitip->user->delete();
            
            // If cascade doesn't work, delete penitip manually
            $penitip->delete();
            
            DB::commit();
            return redirect()->route('dashboard.admin.penitips')
                ->with('success', 'Penitip berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Penitip deletion failed', [
                'error' => $e->getMessage(),
                'penitip_id' => $id
            ]);
            
            return back()->withErrors(['error' => 'Gagal menghapus penitip: ' . $e->getMessage()]);
        }
    }

    public function roles()
    {
        $roles = Role::paginate(10);
        return view('dashboard.admin.roles.index', compact('roles'));
    }

    public function employees()
    {
        $employees = Pegawai::with('user')->paginate(10);
        return view('dashboard.admin.employees.index', compact('employees'));
    }

    public function organizations()
    {
        $organizations = Organisasi::with('user')->paginate(10);
        return view('dashboard.admin.organizations.index', compact('organizations'));
    }
}
