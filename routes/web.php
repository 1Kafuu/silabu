<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PDFGeneratorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;

Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('google-login');

Route::get('/auth/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login-form');

Route::get('/verify', function () {
    return view('auth.otp-verify');
})->name('otp-verify')
->middleware(['auth', 'verified']);

Route::post('/send-otp', [OTPController::class, 'sendOtpEmail'])->name('send-otp');

Route::post('/verified-otp', [OTPController::class, 'verifyOTP'])->name('verified-otp');

Route::get('/pdf-portrait', [PDFGeneratorController::class, 'potrait'])->name('portrait');
Route::get('/pdf-landscape', [PDFGeneratorController::class, 'landscape'])->name('landscape');

Route::post('/pdf-label', [PDFGeneratorController::class, 'label'])->name('label');

Route::get('/label-selected', function () {
    return view('partials._label');
});


Route::get("/dashboard", [HomeController::class, "index"])->name("dashboard")
    ->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user');
    Route::get('/create', [UserController::class, 'create'])->name('create-user');
    Route::post('/store', [UserController::class, 'store'])->name('store-user');
    Route::put('/delete:{id}', [UserController::class, 'delete'])->name('delete-user');
    Route::get('/edit:{id}', [UserController::class, 'edit'])->name('edit-user');
    Route::put('/update:{id}', [UserController::class, 'update'])->name('update-user');
});

Route::middleware(['auth', 'verified'])->prefix('book')->group(function () {
    Route::get('/', [BukuController::class, 'index'])->name('book-list');
    Route::get('/create', [BukuController::class, 'create'])->name('create-book');
    Route::post('/store', [BukuController::class, 'store'])->name('store-book');
    Route::put('/delete:{id}', [BukuController::class, 'delete'])->name('delete-book');
    Route::get('/edit:{id}', [BukuController::class, 'edit'])->name('edit-book');
    Route::put('/update:{id}', [BukuController::class, 'update'])->name('update-book');
});

Route::middleware(['auth', 'verified'])->prefix('category')->group(function () {
    Route::get('/', [KategoriController::class, 'index'])->name('category-list');
    Route::get('/create', [KategoriController::class, 'create'])->name('create-category');
    Route::post('/store', [KategoriController::class, 'store'])->name('store-category');
    Route::put('/delete:{id}', [KategoriController::class, 'delete'])->name('delete-category');
    Route::get('/edit:{id}', [KategoriController::class, 'edit'])->name('edit-category');
    Route::put('/update:{id}', [KategoriController::class, 'update'])->name('update-category');
});

Route::middleware(['auth', 'verified'])->prefix('items')->group(function () {
    Route::get('/', [BarangController::class, 'index'])->name('items-list');
    Route::get('/create', [BarangController::class, 'create'])->name('create-items');
    Route::post('/store', [BarangController::class, 'store'])->name('store-items');
    Route::put('/delete:{id}', [BarangController::class, 'delete'])->name('delete-items');
    Route::get('/edit:{id}', [BarangController::class, 'edit'])->name('edit-items');
    Route::put('/update:{id}', [BarangController::class, 'update'])->name('update-items');
});

Auth::routes();
