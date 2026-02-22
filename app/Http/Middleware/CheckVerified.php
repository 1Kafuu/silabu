<?php
// app/Http/Middleware/CheckVerified.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Route yang boleh diakses tanpa verifikasi
        $excludedRoutes = ['send-otp', 'verified-otp', 'otp-verify'];

        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }

        if (!Auth::check()) {
            return redirect()->route('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (!Auth::user()->isVerified()) {
            return redirect()->route('otp-verify')
                ->with('error', 'Akun Anda belum terverifikasi. Silakan verifikasi email Anda terlebih dahulu.');
        }

        return $next($request);
    }
}