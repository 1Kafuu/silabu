<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 10. Session data (optional - bisa juga pakai Auth::user() langsung)
        $request->session()->put('user', [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->username,
        ]);

        return redirect()->intended($this->redirectTo);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil');
    }
}