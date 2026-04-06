<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifiedRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/')->with([
                'status' => 'danger',
                'message' => 'Silakan login terlebih dahulu.'
            ]);
        }

        $userRoleName = session('user.role_name');

        if (in_array('All', $roles) || in_array($userRoleName, $roles)) {
            return $next($request);
        }

        Auth::logout();
        return redirect('/')->with([
            'status' => 'danger',
            'message' => 'Tidak memiliki akses pada halaman tersebut.'
        ]);
    }
}
