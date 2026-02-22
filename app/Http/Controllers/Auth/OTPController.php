<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OTPController extends Controller
{
    public function verifyOTP(Request $request)
    {
        try {
            // Validasi input OTP harus 6 digit angka
            $request->validate([
                'otp' => 'required|numeric|digits:6'
            ], [
                'otp.required' => 'OTP wajib diisi',
                'otp.numeric' => 'OTP harus berupa angka',
                'otp.digits' => 'OTP harus 6 digit angka'
            ]);

            // Ambil user yang sedang login
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Sesi anda telah berakhir, silakan login kembali');
            }

            // Cek apakah OTP masih berlaku
            if ($user->otp_expires_at && Carbon::now()->gt($user->otp_expires_at)) {
                return redirect()->back()
                    ->with('error', 'OTP telah kadaluarsa, silakan minta OTP baru')
                    ->withInput();
            }

            // Cek apakah OTP yang dimasukkan sesuai
            if ($user->otp !== $request->otp) {
                return redirect()->back()
                    ->withErrors(['otp' => 'Kode OTP yang anda masukkan salah'])
                    ->withInput();
            }

            // Update status user menjadi verified
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
                'email_verified_at' => Carbon::now(),
                'status' => 'verified'
            ]);

            // Redirect ke halaman dashboard
            return redirect()->route('dashboard')
                ->with('success', 'Email anda berhasil diverifikasi!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function sendOtpEmail(Request $request)
    {
        try {
            // Ambil user yang sedang login
            $user = Auth::user();

            if (!$user) {
                // Cek apakah ada email di session (untuk kasus belum login)
                $email = session('email');
                if ($email) {
                    $user = User::where('email', $email)->first();
                }

                if (!$user) {
                    return redirect()->route('login')
                        ->with('error', 'Sesi anda telah berakhir');
                }
            }

            // Generate OTP baru (6 digit)
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Set masa berlaku OTP (5 menit dari sekarang)
            $expiresAt = Carbon::now()->addMinutes(5);

            // Update OTP user
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $expiresAt
            ]);

            // Kirim OTP ke email user
            Mail::to($user->email)->send(new SendEmail($otp));

            // Redirect ke halaman verifikasi OTP
            return redirect()->route('otp-verify')
                ->with('success', 'OTP berhasil dikirim ulang ke email Anda');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim ulang OTP: ' . $e->getMessage());
        }
    }
}
