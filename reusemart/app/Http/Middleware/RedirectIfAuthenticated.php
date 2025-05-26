<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();
            $roleName = $user->role ? strtolower(trim($user->role->nama_role)) : 'pembeli';

            switch ($roleName) {
                case 'owner': 
                    return redirect('/dashboard/owner');
                case 'admin': 
                    return redirect('/dashboard/admin');
                case 'pegawai gudang':
                case 'gudang': 
                    return redirect('/dashboard/warehouse');
                case 'cs': 
                    return redirect('/dashboard/cs');
                case 'penitip':
                    return redirect('/dashboard/consignor');
                case 'organisasi': 
                    return redirect('/dashboard/organization');
                case 'pembeli':
                default:
                    return redirect('/dashboard/buyer');
            }
        }

        return $next($request);
    }
}
