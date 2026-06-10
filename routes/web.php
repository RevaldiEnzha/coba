<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LaundryOrderController;
use App\Http\Controllers\TrackingController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('customers', CustomerController::class);

    Route::resource('orders', LaundryOrderController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::patch('/tracking/{order}', [TrackingController::class, 'updateStatus'])->name('tracking.update');
});

Route::get('/portal', function () {
    return 'Login berhasil. Ini halaman portal pelanggan sementara.';
})->middleware(['auth', 'role:pelanggan'])->name('portal.dashboard');
