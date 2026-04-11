<?php

use App\Http\Middleware\CheckVerified;
use App\Http\Middleware\VerifiedRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'verified' => CheckVerified::class,
            'akses' => VerifiedRole::class,
        ]);
        
        // Exclude Midtrans callback URL from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'midtrans/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
