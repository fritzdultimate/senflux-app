<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;


// Schedule::call(function () {
//     app(DepositService::class)->expireStale();
// })->hourly()->name('expire-stale-deposits')->withoutOverlapping();

// Schedule::call(function () {
//     app(SubscriptionService::class)->expireStale();
// })->hourly()->name('expire-stale-subscriptions')->withoutOverlapping();

// Daily earnings — midnight UTC every day
// Schedule::job(ProcessDailyEarnings::class)
//     ->dailyAt('00:01')
//     ->withoutOverlapping()
//     ->onFailure(function () {
//         \Log::critical('ProcessDailyEarnings job failed!');
//     });

// Rank advancement check — every 6 hours
// Schedule::job(CheckRankAdvancement::class)
//     ->everySixHours()
//     ->withoutOverlapping();

// Sync NowPayments pending deposits — every 5 minutes
// Schedule::job(SyncNowPaymentsStatus::class)
//     ->everyFiveMinutes()
//     ->withoutOverlapping();

// Schedule::job(new SyncTrackedAssetPrices())->everyMinute();

// Schedule::job(DailySlotEarningsService::class)->dailyAt('00:05');

// Schedule::job(SyncFormationMarketData::class)->everyFiveMinutes()->withoutOverlapping();

Schedule::command('formation:detect --batch=25')
    ->everyThreeMinutes()
    ->withoutOverlapping();

Schedule::command('formation:trade-activity:sync')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('formation:snapshot')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('formation:snapshot:prune --days=14')
    ->dailyAt('03:00')
    ->withoutOverlapping();

Schedule::command('formation:health:sweep')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('slot:auto-deploy')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('slot:daily-earnings')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('pack-lifecycle:open-renewal-windows')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

Schedule::command('pack-lifecycle:close-expired-renewal-windows')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
