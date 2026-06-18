<?php

use App\Jobs\CheckRankAdvancement;
use App\Jobs\ProcessDailyEarnings;
use App\Jobs\SyncNowPaymentsStatus;
use App\Services\DepositService;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    app(DepositService::class)->expireStale();
})->hourly()->name('expire-stale-deposits')->withoutOverlapping();

Schedule::call(function () {
    app(SubscriptionService::class)->expireStale();
})->hourly()->name('expire-stale-subscriptions')->withoutOverlapping();

// Daily earnings — midnight UTC every day
Schedule::job(ProcessDailyEarnings::class)
    ->dailyAt('00:01')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Log::critical('ProcessDailyEarnings job failed!');
    });

// Rank advancement check — every 6 hours
Schedule::job(CheckRankAdvancement::class)
    ->everySixHours()
    ->withoutOverlapping();

// Sync NowPayments pending deposits — every 5 minutes
Schedule::job(SyncNowPaymentsStatus::class)
    ->everyFiveMinutes()
    ->withoutOverlapping();
