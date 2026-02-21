<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        // dd($request->all);
        $validate_input = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validate_input->fails()) {
            return redirect()->back()->withErrors($validate_input)->withInput();
        }


        // Multi role authentication
        // $user = User::with(['role_user'=>function($query) {
        //          $query->where('status', 1);
        // }, 'role_user.role'])
        // ->where('email', $request->input('email'))
        // ->first();

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email'=>'Email tidak ditemukan.'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['password'=> 'Password salah.'])->withInput();
        }

        Auth::login($user);

        $request->session()->put('user', [
            'id' => $user->id,
            'email'=> $user->email,
            'name' => $user->username,
        ]);

        return redirect()->intended($this->redirectTo);
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil');
    }
}