<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Business;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Rider;
use App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Auth routes (domain-agnostic: work on central + all tenant subdomains) ──
require __DIR__.'/auth.php';

// ─────────────────────────────────────────────────────────────────────────────
// CENTRAL DOMAIN  (veltrixo.com | veltrixo.test)
// Super admin, business registration, homepage.
// only.central blocks these routes from tenant subdomain access.
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['only.central'])->group(function () {

    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin'    => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    })->name('home');

    // ── Public Business Registration ─────────────────────────────────────────
    Route::get('/business/register',  [Business\RegistrationController::class, 'show'])->name('business.register');
    Route::post('/business/register', [Business\RegistrationController::class, 'store'])->name('business.register.store');
    Route::get('/business/pending',   fn () => Inertia::render('Business/Pending'))->name('business.pending');

    // ── Central dashboard (super admin redirect) ──────────────────────────────
    Route::get('/dashboard', function () {
        if (auth()->user()?->hasRole('super_admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        return redirect()->route('login');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // ── Central profile ───────────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {
        Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // ── Super Admin Panel ─────────────────────────────────────────────────────
    Route::middleware(['auth', 'verified', 'role:super_admin'])
        ->prefix('super-admin')->name('super-admin.')
        ->group(function () {

        Route::get('/dashboard', SuperAdmin\DashboardController::class)->name('dashboard');

        Route::get('/tenants',                                  [SuperAdmin\TenantController::class, 'index'])->name('tenants.index');
        Route::post('/tenants',                                 [SuperAdmin\TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}',                         [SuperAdmin\TenantController::class, 'show'])->name('tenants.show');
        Route::patch('/tenants/{tenant}/suspend',               [SuperAdmin\TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::patch('/tenants/{tenant}/reactivate',            [SuperAdmin\TenantController::class, 'reactivate'])->name('tenants.reactivate');

        Route::get('/plans',           [SuperAdmin\PlanController::class, 'index'])->name('plans.index');
        Route::post('/plans',          [SuperAdmin\PlanController::class, 'store'])->name('plans.store');
        Route::patch('/plans/{plan}',  [SuperAdmin\PlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [SuperAdmin\PlanController::class, 'destroy'])->name('plans.destroy');

        Route::get('/business-verifications',                    [SuperAdmin\VerificationController::class, 'index'])->name('verifications.index');
        Route::get('/business-verifications/{tenant}',           [SuperAdmin\VerificationController::class, 'show'])->name('verifications.show');
        Route::patch('/business-verifications/{tenant}/approve', [SuperAdmin\VerificationController::class, 'approve'])->name('verifications.approve');
        Route::patch('/business-verifications/{tenant}/reject',  [SuperAdmin\VerificationController::class, 'reject'])->name('verifications.reject');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// TENANT SUBDOMAIN  ({subdomain}.veltrixo.com | {subdomain}.veltrixo.test)
// All tenant panels: admin, customer, rider.
// TenantContext is set by InitializeTenancyBySubdomain (global web middleware).
// require.tenant blocks central-domain access to these routes.
// tenant.active blocks suspended / unverified tenants.
// tenant.user prevents cross-tenant user access on authenticated routes.
// ─────────────────────────────────────────────────────────────────────────────
Route::domain('{subdomain}.' . config('tenancy.app_domain'))
    ->middleware(['require.tenant', 'tenant.active'])
    ->group(function () {

    // Tenant root
    Route::get('/', fn () => auth()->check()
        ? redirect()->route('tenant.dashboard')
        : redirect()->route('login')
    )->name('tenant.home');

    // Tenant dashboard router (dispatches to role-specific dashboard)
    Route::get('/dashboard', function (string $subdomain) {
        $user = auth()->user();
        if ($user->hasRole('admin'))  return redirect()->route('admin.dashboard', ['subdomain' => $subdomain]);
        if ($user->hasRole('rider'))  return redirect()->route('rider.dashboard', ['subdomain' => $subdomain]);
        return redirect()->route('customer.dashboard', ['subdomain' => $subdomain]);
    })->middleware(['auth', 'verified', 'tenant.user'])->name('tenant.dashboard');

    // ── Tenant profile ────────────────────────────────────────────────────────
    Route::middleware(['auth', 'tenant.user'])->group(function () {
        Route::get('/profile',    [ProfileController::class, 'edit'])->name('tenant.profile.edit');
        Route::patch('/profile',  [ProfileController::class, 'update'])->name('tenant.profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('tenant.profile.destroy');
    });

    // ── Admin Panel ───────────────────────────────────────────────────────────
    Route::middleware(['auth', 'verified', 'role:admin', 'tenant.user'])
        ->prefix('admin')->name('admin.')
        ->group(function () {

        Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

        Route::prefix('deliveries')->name('deliveries.')->group(function () {
            Route::get('/',                   [Admin\DeliveryController::class, 'index'])->name('index');
            Route::post('/generate',          [Admin\DeliveryController::class, 'generate'])->name('generate');
            Route::post('/bulk-assign',       [Admin\DeliveryController::class, 'bulkAssign'])->name('bulk-assign');
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

        Route::resource('products', Admin\ProductController::class);

        Route::prefix('wallets')->name('wallets.')->group(function () {
            Route::get('/',                               [Admin\WalletController::class, 'index'])->name('index');
            Route::post('/users/{user}/credit',           [Admin\WalletController::class, 'credit'])->name('credit');
            Route::patch('/recharges/{recharge}/approve', [Admin\WalletController::class, 'approveRecharge'])->name('recharges.approve');
            Route::patch('/recharges/{recharge}/reject',  [Admin\WalletController::class, 'rejectRecharge'])->name('recharges.reject');
        });

        Route::prefix('riders')->name('riders.')->group(function () {
            Route::get('/',           [Admin\RiderController::class, 'index'])->name('index');
            Route::post('/',          [Admin\RiderController::class, 'store'])->name('store');
            Route::get('/{rider}',    [Admin\RiderController::class, 'show'])->name('show');
            Route::patch('/{rider}',  [Admin\RiderController::class, 'update'])->name('update');
            Route::delete('/{rider}', [Admin\RiderController::class, 'destroy'])->name('destroy');
        });

        Route::get('/analytics', Admin\AnalyticsController::class)->name('analytics');
    });

    // ── Customer Portal ───────────────────────────────────────────────────────
    Route::middleware(['auth', 'verified', 'role:customer', 'tenant.user'])
        ->prefix('customer')->name('customer.')
        ->group(function () {

        Route::get('/dashboard', Customer\DashboardController::class)->name('dashboard');

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

        Route::get('/wallet',           [Customer\WalletController::class, 'show'])->name('wallet');
        Route::post('/wallet/recharge', [Customer\WalletController::class, 'recharge'])->name('wallet.recharge');
    });

    // ── Rider App ─────────────────────────────────────────────────────────────
    Route::middleware(['auth', 'verified', 'role:rider', 'tenant.user'])
        ->prefix('rider')->name('rider.')
        ->group(function () {

        Route::get('/dashboard', Rider\DashboardController::class)->name('dashboard');

        Route::get('/deliveries',                      [Rider\DeliveryController::class, 'index'])->name('deliveries.index');
        Route::post('/deliveries/{delivery}/start',    [Rider\DeliveryController::class, 'start'])->name('deliveries.start');
        Route::post('/deliveries/{delivery}/complete', [Rider\DeliveryController::class, 'complete'])->name('deliveries.complete');
        Route::post('/location',                       [Rider\DeliveryController::class, 'updateLocation'])->name('location');
    });
});
