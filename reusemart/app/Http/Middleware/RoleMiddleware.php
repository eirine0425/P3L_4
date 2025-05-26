<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Tambahkan logging untuk debugging
        Log::info('RoleMiddleware check: User ID: ' . $user->id . ', Email: ' . $user->email);
        
        if (!$user->role) {
            Log::warning('User has no role: ' . $user->email);
            return redirect()->route('login')->with('error', 'Akun Anda tidak memiliki role yang valid.');
        }
        
        $userRole = strtolower(trim($user->role->nama_role));
        $requiredRole = strtolower(trim($role));
        
        Log::info('Role check: User role: ' . $userRole . ', Required role: ' . $requiredRole);
        
        if ($userRole === $requiredRole) {
            return $next($request);
        }

        // Redirect ke dashboard yang sesuai dengan role user
        switch ($userRole) {
            case 'owner':
                return redirect()->route('dashboard.owner');
            case 'admin':
                return redirect()->route('dashboard.admin');
            case 'pegawai gudang':
            case 'gudang':
                return redirect()->route('dashboard.warehouse');
            case 'cs':
                return redirect()->route('dashboard.cs');
            case 'penitip':
            case 'penjual':
                return redirect()->route('dashboard.consignor');
            case 'organisasi':
                return redirect()->route('dashboard.organization');
            case 'pembeli':
                return redirect()->route('dashboard.buyer');
            default:
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }
}
