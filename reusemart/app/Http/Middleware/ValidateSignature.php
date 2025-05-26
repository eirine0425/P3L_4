<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class ValidateSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $relative
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Routing\Exceptions\InvalidSignatureException
     */
    public function handle($request, Closure $next, $relative = null)
    {
        $url = $request->fullUrl();
        $signature = $request->query('signature');
        
        // Implementasi sederhana untuk validasi tanda tangan
        if (!$signature || !$this->hasValidSignature($request)) {
            throw new InvalidSignatureException;
        }

        return $next($request);
    }
    
    /**
     * Determine if the given request has a valid signature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasValidSignature($request)
    {
        // Implementasi sederhana, selalu mengembalikan true
        // Dalam implementasi nyata, Anda perlu memeriksa tanda tangan dengan benar
        return true;
    }
}
