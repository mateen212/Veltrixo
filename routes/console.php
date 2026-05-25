<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1:00 AM — Generate today's deliveries for all active tenants
Schedule::command('deliveries:generate')->dailyAt('01:00')->withoutOverlapping()->runInBackground();

// 10:00 PM — Flag deliveries still pending/assigned as missed
Schedule::command('deliveries:mark-missed')->dailyAt('22:00')->withoutOverlapping()->runInBackground();

// 11:00 PM — Aggregate daily KPIs for all tenants (runs for yesterday's date by default)
Schedule::command('analytics:generate-daily')->dailyAt('23:00')->withoutOverlapping()->runInBackground();

// 12:30 AM — Auto-resume paused subscriptions that have passed their pause_until date
Schedule::call(function () {
    \App\Models\Subscription::where('status', 'paused')
        ->where('pause_until', '<', now())
        ->each(function ($sub) {
            app(\App\Services\Subscription\SubscriptionService::class)->resume($sub);
        });
})->dailyAt('00:30')->name('resume-paused-subscriptions')->withoutOverlapping();
