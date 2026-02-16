<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\User;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('auth.login');
// });
Route::get("/", function () {
    return view("auth.login");
});

Route::get("/dashboard", [HomeController::class,"index"])->name("dashboard")
->middleware('auth');

Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/', [UserController::class,'index'])->name('user');
    Route::get('/create', [UserController::class,'create'])->name('create-user');
    Route::post('/store', [UserController::class,'store'])->name('store-user');
    Route::put('/delete:{id}', [UserController::class,'delete'])->name('delete-user');
    Route::get('/edit:{id}', [UserController::class,'edit'])->name('edit-user');
    Route::put('/update:{id}', [UserController::class,'update'])->name('update-user');
});

Route::middleware(['auth'])->prefix('book')->group(function () {
    Route::get('/', [BukuController::class, 'index'])->name('book-list');
    Route::get('/create', [BukuController::class,'create'])->name('create-book');
    Route::post('/store', [BukuController::class,'store'])->name('store-book');
    Route::put('/delete:{id}', [BukuController::class,'delete'])->name('delete-book');
    Route::get('/edit:{id}', [BukuController::class,'edit'])->name('edit-book');
    Route::put('/update:{id}', [BukuController::class,'update'])->name('update-book');
});

Route::middleware('auth')->prefix('category')->group(function () {
    Route::get('/', [KategoriController::class, 'index'])->name('category-list');
    Route::get('/create', [KategoriController::class,'create'])->name('create-category');
    Route::post('/store', [KategoriController::class,'store'])->name('store-category');
    Route::put('/delete:{id}', [KategoriController::class,'delete'])->name('delete-category');
    Route::get('/edit:{id}', [KategoriController::class,'edit'])->name('edit-category');
    Route::put('/update:{id}', [KategoriController::class,'update'])->name('update-category');
});

Route::get('/error-404', function () {
    return view('pages.samples.error-404');
})->name('error-404');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
