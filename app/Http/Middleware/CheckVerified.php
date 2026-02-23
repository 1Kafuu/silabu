<?php
// app/Http/Middleware/CheckVerified.php

namespace App\Http\Middleware;

use App\Mail\SendEmail;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah user sudah verified
        if (Auth::user()->isVerified()) {
            // Jika user sudah verified dan mencoba mengakses route verify, redirect ke dashboard
            if ($request->routeIs('otp-verify') || $request->is('verify*') || $request->is('otp*')) {
                return redirect()->route('dashboard')->with('info', 'Akun Anda sudah terverifikasi.');
            }
            return $next($request);
        }

        // User belum verified dan mengakses route selain verify
        if (!$request->routeIs('otp-verify') && !$request->is('verify*') && !$request->is('otp*')) {
            $user = Auth::user();
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // Set masa berlaku OTP (5 menit)
            $expiresAt = Carbon::now()->addMinutes(5);
            // Update OTP user
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $expiresAt
            ]);
            
            Mail::to($user->email)->send(new SendEmail($otp));

            return redirect()->route('otp-verify')
                ->with('error', 'Akun Anda belum terverifikasi. Silakan check email Anda untuk mendapatkan kode OTP.');
        }

        return $next($request);
    }
}