<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LaundryOrderController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DeliveryRequestController;
use App\Http\Controllers\CustomerPortalController;


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


    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{invoice}/process', [PaymentController::class, 'process'])->name('payments.process');

    Route::get('/delivery-requests', [DeliveryRequestController::class, 'index'])->name('delivery.index');
    Route::patch('/delivery-requests/{deliveryRequest}', [DeliveryRequestController::class, 'updateStatus'])->name('delivery.update');

    Route::post('/delivery-requests/{deliveryRequest}/confirm', [DeliveryRequestController::class, 'confirm'])
    ->name('delivery.confirm');

});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});


Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/portal', [CustomerPortalController::class, 'index'])->name('portal.dashboard');
    Route::get('/portal/active', [CustomerPortalController::class, 'active'])->name('portal.active');
    Route::get('/portal/history', [CustomerPortalController::class, 'history'])->name('portal.history');
    Route::get('/portal/points', [CustomerPortalController::class, 'points'])->name('portal.points');
    
    // Route Manajemen Akun
    Route::get('/portal/account', [CustomerPortalController::class, 'account'])->name('portal.account');
    Route::post('/portal/account/update', [CustomerPortalController::class, 'updateAccount'])->name('portal.account.update');
    
    Route::get('/portal/orders/{order}', [CustomerPortalController::class, 'show'])->name('portal.orders.show');
    Route::post('/portal/pickups', [DeliveryRequestController::class, 'store'])->name('portal.pickups.store');
});