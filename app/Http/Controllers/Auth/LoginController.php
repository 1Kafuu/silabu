<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Rate limiting
        $key = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                ->withErrors(['email' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.'])
                ->withInput();
        }

        // 2. Validasi input
        $validate_input = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validate_input->fails()) {
            return redirect()->back()->withErrors($validate_input)->withInput();
        }

        // 3. Generic error message untuk keamanan (prevent user enumeration)
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (!Auth::attempt($credentials, $remember)) {
            // 4. Increment rate limiter on failure
            RateLimiter::hit($key, 60);

            // 5. Log failed attempt
            \Log::warning('Failed login attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->withInput();
        }

        // 6. Regenerate session setelah login sukses
        $request->session()->regenerate();

        // 7. Clear rate limiter on success
        RateLimiter::clear($key);

        // 8. Log successful login
        \Log::info('Successful login', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // 9. Update last login timestamp
        $user = Auth::user();
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        // 10. Session data (optional)
        $request->session()->put('user', [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'status' => $user->status,
        ]);

        // 11. CEK STATUS USER
        // Jika user status 'active' (belum verifikasi email) -> redirect ke OTP
        if ($user->status === 'active' && !$user->hasVerifiedEmail()) {
            // Generate OTP baru untuk verifikasi
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Set masa berlaku OTP (5 menit)
            $expiresAt = Carbon::now()->addMinutes(5);

            // Update OTP user
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $expiresAt
            ]);

            // Kirim OTP ke email user
            try {
                Mail::to($user->email)->send(new SendEmail($otp));

                // Untuk testing, simpan ke log
                \Log::info('OTP for user ' . $user->email . ': ' . $otp);

                // Redirect ke halaman verifikasi OTP
                return redirect()->route('otp-verify')
                    ->with('success', 'Please check your email for OTP code to verify your account.');

            } catch (\Exception $e) {
                \Log::error('Failed to send OTP email: ' . $e->getMessage());

                return redirect()->route('otp-verify')
                    ->with('warning', 'Failed to send OTP email. Please click resend button.');
            }
        }

        // Jika user status 'verified' (email sudah terverifikasi) -> redirect ke dashboard
        if ($user->status === 'verified' || $user->hasVerifiedEmail()) {
            // Update status user jika perlu
            if ($user->status !== 'verified') {
                $user->update(['status' => 'verified']);
            }

            return redirect()->intended($this->redirectTo)
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Jika status lain (misal: suspended, inactive) -> logout dan beri pesan
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('/')
            ->withErrors(['email' => 'Your account is not active. Please contact support.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil');
    }
}