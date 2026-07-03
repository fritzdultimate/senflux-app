<?php

use App\Http\Controllers\CronJob\RunFormationAutoDetectionController;
use Illuminate\Support\Facades\Route;

Route::prefix('cron')->name('cron.')->group(function () {
    Route::get('/formation/detect', [RunFormationAutoDetectionController::class, 'run'])->name('formations.share');
});