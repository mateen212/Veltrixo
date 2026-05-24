<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Models\Product;
use App\Models\Category;

// ─── Auth / Profile (Sanctum) ─────────────────────────────────────────────────
Route::get('/user', fn (Request $request) => $request->user())
    ->middleware('auth:sanctum')
    ->name('api.user');

// ─── Public Catalog API ───────────────────────────────────────────────────────
Route::prefix('catalog')->name('api.catalog.')->group(function () {
    Route::get('/products', function (Request $request) {
        $products = Product::active()
            ->when($request->tenant_id, fn ($q) => $q->forTenant($request->tenant_id))
            ->with(['category', 'variants'])
            ->paginate(20);
        return ProductResource::collection($products);
    })->name('products');

    Route::get('/categories', function (Request $request) {
        $categories = Category::active()
            ->roots()
            ->when($request->tenant_id, fn ($q) => $q->where('tenant_id', $request->tenant_id))
            ->with('children')
            ->get();
        return CategoryResource::collection($categories);
    })->name('categories');
});

// ─── Authenticated API ────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.v1.')->group(function () {

    // Rider location update
    Route::post('/rider/location', [App\Http\Controllers\Rider\DeliveryController::class, 'updateLocation'])
        ->middleware('role:rider')
        ->name('rider.location');

    // Delivery OTP verify (public-ish — delivery token based)
    Route::post('/deliveries/verify-otp', function (Request $request) {
        $validated = $request->validate([
            'qr_code_token' => ['required', 'string'],
            'otp'           => ['required', 'string', 'size:6'],
        ]);

        $delivery = \App\Models\Delivery::where('qr_code_token', $validated['qr_code_token'])->firstOrFail();

        $verified = app(\App\Services\Delivery\DeliveryService::class)
            ->verifyOtp($delivery, $validated['otp']);

        return response()->json(['verified' => $verified]);
    })->name('deliveries.verify-otp');
});
