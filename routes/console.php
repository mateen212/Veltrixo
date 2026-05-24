<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily delivery generation — runs at 1 AM every day
Schedule::command('deliveries:generate')->dailyAt('01:00')->withoutOverlapping();

// Process auto-resume for paused subscriptions past their pause_until date
Schedule::call(function () {
    \App\Models\Subscription::where('status', 'paused')
        ->where('pause_until', '<', now())
        ->each(function ($sub) {
            app(\App\Services\Subscription\SubscriptionService::class)->resume($sub);
        });
})->dailyAt('00:30')->name('resume-paused-subscriptions')->withoutOverlapping();
