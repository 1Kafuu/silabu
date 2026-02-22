<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Socialite;

class GoogleAuthController extends Controller
{
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $data = [
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
            ];

            // Cek apakah user sudah ada
            $existingUser = User::where('email', $googleUser->getEmail())->where('status', 'verified')->first();

            if (!$existingUser) {
                // User baru, tambahkan status
                $data['status'] = 'active';
            }

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                $data
            );

            Auth::login($user);

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            return $this->redirectBasedOnStatus($user);

        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage());

            return redirect('/')->withErrors([
                'email' => 'Google authentication failed. Please try again.'
            ]);
        }
    }

    private function redirectBasedOnStatus($user)
    {
        if ($user->status === 'verified') {
            return redirect()->intended('/dashboard');
        }

        // Untuk status yang bukan verified (active, pending, dll), kirim OTP
        try {
            // Generate OTP baru (6 digit)
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Set masa berlaku OTP (5 menit dari sekarang)
            $expiresAt = Carbon::now()->addMinutes(5);

            // Update OTP user
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $expiresAt,
                'otp_attempts' => 0 // Reset percobaan OTP jika ada kolom
            ]);

            // Kirim OTP ke email user
            Mail::to($user->email)->send(new SendEmail ($otp));

            // Simpan informasi pengiriman ke session (opsional)
            session([
                'otp_sent_at' => now(),
                'otp_email' => $user->email
            ]);

            // Log pengiriman OTP
            \Log::info('OTP sent to user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => $user->status,
                'expires_at' => $expiresAt
            ]);

            // Redirect ke halaman verifikasi dengan pesan sukses
            return redirect()->intended('/verify')
                ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox atau folder spam.')
                ->with('otp_expires_at', $expiresAt);

        } catch (\Exception $e) {
            // Log error jika pengiriman OTP gagal
            \Log::error('Failed to send OTP email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            // Redirect ke halaman verifikasi dengan pesan warning
            return redirect()->intended('/verify')
                ->with('warning', 'Gagal mengirim kode OTP. Silakan klik tombol "Resend OTP" untuk mengirim ulang.')
                ->with('otp_error', true);
        }
    }
}
