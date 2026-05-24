<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Rider;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin Panel ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

    Route::prefix('deliveries')->name('deliveries.')->group(function () {
        Route::get('/',             [Admin\DeliveryController::class, 'index'])->name('index');
        Route::post('/generate',    [Admin\DeliveryController::class, 'generate'])->name('generate');
        Route::post('/bulk-assign', [Admin\DeliveryController::class, 'bulkAssign'])->name('bulk-assign');
        Route::post('/{delivery}/assign', [Admin\DeliveryController::class, 'assign'])->name('assign');
        Route::post('/{delivery}/missed', [Admin\DeliveryController::class, 'markMissed'])->name('missed');
    });

    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/',                       [Admin\SubscriptionController::class, 'index'])->name('index');
        Route::get('/{subscription}',         [Admin\SubscriptionController::class, 'show'])->name('show');
        Route::post('/{subscription}/cancel', [Admin\SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/{subscription}/pause',  [Admin\SubscriptionController::class, 'pause'])->name('pause');
        Route::post('/{subscription}/resume', [Admin\SubscriptionController::class, 'resume'])->name('resume');
    });

    Route::apiResource('products', Admin\ProductController::class);

    Route::prefix('wallets')->name('wallets.')->group(function () {
        Route::get('/',                                  [Admin\WalletController::class, 'index'])->name('index');
        Route::post('/users/{user}/credit',              [Admin\WalletController::class, 'credit'])->name('credit');
        Route::post('/recharges/{recharge}/approve',     [Admin\WalletController::class, 'approveRecharge'])->name('recharges.approve');
        Route::post('/recharges/{recharge}/reject',      [Admin\WalletController::class, 'rejectRecharge'])->name('recharges.reject');
    });
});

// ─── Customer Portal ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Customer/Dashboard'))->name('dashboard');

    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/',                       [Customer\SubscriptionController::class, 'index'])->name('index');
        Route::post('/',                      [Customer\SubscriptionController::class, 'store'])->name('store');
        Route::get('/{subscription}',         [Customer\SubscriptionController::class, 'show'])->name('show');
        Route::post('/{subscription}/pause',  [Customer\SubscriptionController::class, 'pause'])->name('pause');
        Route::post('/{subscription}/resume', [Customer\SubscriptionController::class, 'resume'])->name('resume');
        Route::post('/{subscription}/cancel', [Customer\SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/{subscription}/skip',   [Customer\SubscriptionController::class, 'skip'])->name('skip');
    });

    Route::get('/deliveries',            [Customer\DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{delivery}', [Customer\DeliveryController::class, 'show'])->name('deliveries.show');

    Route::get('/wallet',             [Customer\WalletController::class, 'show'])->name('wallet');
    Route::post('/wallet/recharge',   [Customer\WalletController::class, 'recharge'])->name('wallet.recharge');
});

// ─── Rider App ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:rider'])->prefix('rider')->name('rider.')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Rider/Dashboard'))->name('dashboard');

    Route::get('/deliveries',              [Rider\DeliveryController::class, 'index'])->name('deliveries.index');
    Route::post('/deliveries/{delivery}/start',    [Rider\DeliveryController::class, 'start'])->name('deliveries.start');
    Route::post('/deliveries/{delivery}/complete', [Rider\DeliveryController::class, 'complete'])->name('deliveries.complete');
    Route::post('/location',               [Rider\DeliveryController::class, 'updateLocation'])->name('location');
});

require __DIR__.'/auth.php';
