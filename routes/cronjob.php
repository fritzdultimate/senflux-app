<?php

use App\Http\Controllers\CronJob\RunFormationAutoDetectionController;
use App\Http\Controllers\CronJob\SyncFormationTradeActivityController;
use Illuminate\Support\Facades\Route;

Route::middleware('cron.secret')->prefix('cron')->name('cron.')->group(function () {
    Route::get('/formation/detect', [RunFormationAutoDetectionController::class, 'run'])->name('formations.detect');

    Route::get('/formation/trade-activity/sync', [SyncFormationTradeActivityController::class, 'run'])->name('formations.trade-activity.sync');

    Route::get('/formations/snapshot', [RunFormationAutoDetectionController::class, 'snapshot']);
});