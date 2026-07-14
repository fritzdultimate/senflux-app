<?php

use App\Http\Controllers\CronJob\DailySlotEarningsController;
use App\Http\Controllers\CronJob\FormationsFromCoinGeckoController;
use App\Http\Controllers\CronJob\RunFormationAutoDetectionController;
use App\Http\Controllers\CronJob\SlotAutoDeploymentController;
use App\Http\Controllers\CronJob\SyncFormationTradeActivityController;
use Illuminate\Support\Facades\Route;

// middleware('cron.secret')->
Route::prefix('cron')->name('cron.')->group(function () {
    Route::get('/formation/detect', [RunFormationAutoDetectionController::class, 'run'])->name('formations.detect');

    Route::get('/formation/trade-activity/sync', [SyncFormationTradeActivityController::class, 'run'])->name('formations.trade-activity.sync');

    Route::get('/formations/snapshot', [RunFormationAutoDetectionController::class, 'snapshot']);

    Route::get('/formations/snapshot/prune', [RunFormationAutoDetectionController::class, 'pruneSnapshot']);


    Route::get('/coingecko', [FormationsFromCoinGeckoController::class, 'run']);
    Route::get('/coingecko/reset', [FormationsFromCoinGeckoController::class, 'reset']);


    Route::get('/formation/slot/auto-deploy', [SlotAutoDeploymentController::class, 'deployEligible']);
    Route::get('/formation/sweep', [SlotAutoDeploymentController::class, 'sweep']);

    Route::get('/slot/daily-profit', [DailySlotEarningsController::class, 'process']);

});