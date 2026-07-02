<?php

use App\Jobs\CheckRankAdvancement;
use App\Jobs\ProcessDailyEarnings;
use App\Jobs\SyncFormationMarketData;
use App\Jobs\SyncNowPaymentsStatus;
use App\Services\DailySlotEarningsService;
use App\Services\DepositService;
use Illuminate\Support\Facades\Schedule;


Schedule::call(function () {
    app(DepositService::class)->expireStale();
})->hourly()->name('expire-stale-deposits')->withoutOverlapping();

// Schedule::call(function () {
//     app(SubscriptionService::class)->expireStale();
// })->hourly()->name('expire-stale-subscriptions')->withoutOverlapping();

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

// Schedule::job(new SyncTrackedAssetPrices())->everyMinute();

Schedule::job(DailySlotEarningsService::class)->dailyAt('00:05');

Schedule::job(SyncFormationMarketData::class)->everyFiveMinutes()->withoutOverlapping();
