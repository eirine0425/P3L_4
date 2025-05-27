<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MultiRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role ? strtolower(trim($user->role->nama_role)) : '';
        
        // Periksa apakah pengguna memiliki salah satu peran yang diperlukan
        foreach ($roles as $role) {
            $requiredRole = strtolower(trim($role));
            
            // Support alias untuk pegawai gudang
            if (($requiredRole === 'pegawai gudang' || $requiredRole === 'gudang') && 
                ($userRole === 'pegawai gudang' || $userRole === 'gudang')) {
                return $next($request);
            }
            
            if ($userRole === $requiredRole) {
                return $next($request);
            }
        }
        
        // Jika tidak memiliki peran yang diperlukan, redirect ke dashboard
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
