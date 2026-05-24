<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Subscription\SubscriptionService::class,
            \App\Services\Subscription\SubscriptionService::class
        );
        $this->app->bind(
            \App\Services\Delivery\DeliveryService::class,
            \App\Services\Delivery\DeliveryService::class
        );
        $this->app->bind(
            \App\Services\Wallet\WalletService::class,
            \App\Services\Wallet\WalletService::class
        );
        $this->app->bind(
            \App\Services\Analytics\AnalyticsService::class,
            \App\Services\Analytics\AnalyticsService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
