<?php

namespace App\Providers;

use App\Models\Delivery;
use App\Observers\DeliveryObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        $this->app->bind(
            \App\Services\Notification\NotificationService::class,
            \App\Services\Notification\NotificationService::class
        );
        $this->app->bind(
            \App\Services\Tenant\TenantProvisioningService::class,
            \App\Services\Tenant\TenantProvisioningService::class
        );
        $this->app->bind(
            \App\Services\Report\ReportService::class,
            \App\Services\Report\ReportService::class
        );
        $this->app->bind(
            \App\Services\Product\ProductService::class,
            \App\Services\Product\ProductService::class
        );
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Register model observers
        Delivery::observe(DeliveryObserver::class);
    }
}

