<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PDFGeneratorController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\MidtransController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;

Route::get('/auth/redirect', [GoogleAuthController::class, 'redirectToProvider'])->name('google-login');

Route::get('/auth/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login-form');

Route::get('/verify', function () {
    return view('auth.otp-verify');
})->name('otp-verify')
->middleware(['verified']);

Route::post('/send-otp', [OTPController::class, 'sendOtpEmail'])->name('send-otp');

Route::post('/verified-otp', [OTPController::class, 'verifyOTP'])->name('verified-otp');

Route::get('/pdf-portrait', [PDFGeneratorController::class, 'potrait'])->name('portrait');
Route::get('/pdf-landscape', [PDFGeneratorController::class, 'landscape'])->name('landscape');

Route::post('/pdf-label', [PDFGeneratorController::class, 'label'])->name('label');

Route::get('/label-selected', function () {
    return view('partials._label');
});

Route::get("/dashboard", [HomeController::class, "index"])->name("dashboard")
    ->middleware(['verified', 'akses:Admin']);

Route::middleware(['verified', 'akses:Admin'])->prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user');
    Route::get('/create', [UserController::class, 'create'])->name('create-user');
    Route::post('/store', [UserController::class, 'store'])->name('store-user');
    Route::put('/delete:{id}', [UserController::class, 'delete'])->name('delete-user');
    Route::get('/edit:{id}', [UserController::class, 'edit'])->name('edit-user');
    Route::put('/update:{id}', [UserController::class, 'update'])->name('update-user');
    Route::get('/manage:{id}', [UserController::class, 'manage'])->name('manage-role');
    Route::post('/assign-role:{id}', [UserController::class, 'assignRole'])->name('assign-role');
    Route::post('/update-roles:{id}', [UserController::class, 'updateRoles'])->name('update-roles');
    Route::put('/set-active-role:{userId}/{roleUserId}', [UserController::class, 'setActiveRole'])->name('set-active-role');
    Route::put('/set-inactive-role:{userId}/{roleUserId}', [UserController::class, 'setInactiveRole'])->name('set-inactive-role');
    Route::delete('/remove-role:{userId}/{roleUserId}', [UserController::class, 'removeRole'])->name('remove-role');
});

Route::middleware(['verified', 'akses:Admin'])->prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('role');
    Route::get('/create', [RoleController::class, 'create'])->name('create-role');
    Route::post('/store', [RoleController::class, 'store'])->name('store-role');
    Route::put('/delete:{id}', [RoleController::class, 'delete'])->name('delete-role');
    Route::get('/edit:{id}', [RoleController::class, 'edit'])->name('edit-role');
    Route::put('/update:{id}', [RoleController::class, 'update'])->name('update-role');
});

Route::middleware(['verified', 'akses:Admin'])->prefix('book')->group(function () {
    Route::get('/', [BukuController::class, 'index'])->name('book-list');
    Route::get('/create', [BukuController::class, 'create'])->name('create-book');
    Route::post('/store', [BukuController::class, 'store'])->name('store-book');
    Route::put('/delete:{id}', [BukuController::class, 'delete'])->name('delete-book');
    Route::get('/edit:{id}', [BukuController::class, 'edit'])->name('edit-book');
    Route::put('/update:{id}', [BukuController::class, 'update'])->name('update-book');
});

Route::middleware(['verified', 'akses:Admin'])->prefix('category')->group(function () {
    Route::get('/', [KategoriController::class, 'index'])->name('category-list');
    Route::get('/create', [KategoriController::class, 'create'])->name('create-category');
    Route::post('/store', [KategoriController::class, 'store'])->name('store-category');
    Route::put('/delete:{id}', [KategoriController::class, 'delete'])->name('delete-category');
    Route::get('/edit:{id}', [KategoriController::class, 'edit'])->name('edit-category');
    Route::put('/update:{id}', [KategoriController::class, 'update'])->name('update-category');
});

Route::middleware(['verified', 'akses:Admin'])->prefix('vendor')->group(function() {
    Route::get('/', [VendorController::class, 'index'])->name('vendor-list');
    Route::get('/create', [VendorController::class, 'create'])->name('create-vendor');
    Route::post('/store', [VendorController::class, 'store'])->name('store-vendor');
    Route::put('/delete:{id}', [VendorController::class, 'delete'])->name('delete-vendor');
    Route::get('/edit:{id}', [VendorController::class, 'edit'])->name('edit-vendor');
    Route::put('/update:{id}', [VendorController::class, 'update'])->name('update-vendor');
});

Route::middleware(['verified', 'akses:Admin'])->prefix('items')->group(function () {
    Route::get('/', [BarangController::class, 'index'])->name('items-list');
    Route::get('/create', [BarangController::class, 'create'])->name('create-items');
    Route::post('/store', [BarangController::class, 'store'])->name('store-items');
    Route::put('/delete:{id}', [BarangController::class, 'delete'])->name('delete-items');
    Route::get('/edit:{id}', [BarangController::class, 'edit'])->name('edit-items');
    Route::put('/update:{id}', [BarangController::class, 'update'])->name('update-items');
});

Route::middleware(['verified', 'akses:Admin'])->prefix('shipment')->group(function () {
    Route::get('/', function () { 
        return view('admin.shipment.shipment'); 
    })->name('shipment');
    Route::get('/datatables', function () {
        return view('admin.shipment.shipment-datatables');
    })->name('shipment-datatables');
});

Route::middleware(['verified', 'akses:Admin'])->prefix('wilayah')->group(function () {
    Route::get('/axios', [WilayahController::class, 'indexAxios'])->name('wilayah-axios');
    Route::get('/ajax', [WilayahController::class, 'indexAjax'])->name('wilayah-ajax');
    Route::post('/get-kota', [WilayahController::class, 'getKota'])->name('get-kota');
    Route::post('/get-kecamatan', [WilayahController::class, 'getKecamatan'])->name('get-kecamatan');
    Route::post('/get-kelurahan', [WilayahController::class, 'getKelurahan'])->name('get-kelurahan');
});

Route::middleware(['verified', 'akses:Admin'])->prefix('pos')->group(function () {
    Route::get('/axios', [POSController::class, 'indexAxios'])->name('pos-axios');
    Route::get('/ajax', [POSController::class, 'indexAjax'])->name('pos-ajax');
    Route::post('/store', [POSController::class, 'store'])->name('pos-store');
});

Route::middleware(['auth', 'verified', 'akses:Admin'])->prefix('kota')->group(function () {
    Route::get('/', function () { 
        return view('admin.kota.kota'); 
    })->name('kota');
});

Route::prefix('customer')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customer-list');
    Route::post('/store', [CustomerController::class, 'store'])->name('store-pesanan');
});

// Midtrans Callback URL
// This route receives payment notifications from Midtrans
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])->name('midtrans.callback');

// Midtrans pay pending order
Route::post('/midtrans/pay', [MidtransController::class, 'payPending'])->name('midtrans.pay');

Route::middleware(['verified','akses:Admin,Vendor'])->prefix('vendor')->group(function () {
    Route::get('/menu', [VendorController::class, 'menu'])->name('menu-list');
    Route::get('/pesanan', [VendorController::class, 'pesanan'])->name('vendor-pesanan');
    Route::get('/menu/create', [VendorController::class, 'createMenu'])->name('create-menu');
    Route::get('/menu/edit/{id}', [VendorController::class, 'editMenu'])->name('edit-menu');
    Route::put('/menu/update/{id}', [VendorController::class, 'updateMenu'])->name('update-menu');
    Route::put('/menu/delete/{id}', [VendorController::class, 'deleteMenu'])->name('delete-menu');
    Route::post('/menu/store', [VendorController::class, 'store'])->name('store-menu');
});

Auth::routes();
