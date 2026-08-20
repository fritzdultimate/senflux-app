<?php

use App\Models\Formation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;


// Schedule::call(function () {
//     app(DepositService::class)->expireStale();
// })->hourly()->name('expire-stale-deposits')->withoutOverlapping();

// Schedule::call(function () {
//     app(SubscriptionService::class)->expireStale();
// })->hourly()->name('expire-stale-subscriptions')->withoutOverlapping();


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

Schedule::command('bonus:confirmation')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('birdeye:sync --batch=1')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('telescope:prune --hours=48')->daily();

Schedule::call(function () {
    $today = Formation::active()
        ->whereIn('state', ['active', 'matured'])
        ->sum('active_wallets');

    Cache::put('active_wallets_yesterday', $today, now()->addDays(2));
})->dailyAt('00:00'); 
