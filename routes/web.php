<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Webhook\NowPaymentsWebhookController;
use App\Livewire\Auth\CollectEmail;
use App\Livewire\Pages\About;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\HowItWorks;
use App\Livewire\Pages\MarketInsights;
use App\Livewire\Protected\Dashboard;
use App\Livewire\Protected\Deposit\CreateDeposit;
use App\Livewire\Protected\Deposit\DepositTracker;
use App\Livewire\Protected\Subscription\Subscribe;
use App\Livewire\Protected\Subscription\SubscriptionTracker;
use App\Livewire\Protected\Terminal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/',               Home::class)->name('home');
Route::get('/about',          About::class)->name('about');
Route::get('/web/terminal',       \App\Livewire\Pages\Terminal::class)->name('terminal');
Route::get('/how-it-works',   HowItWorks::class)->name('how-it-works');
Route::get('/market-insights',MarketInsights::class)->name('market-insights');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback');

Route::middleware('auth')->group(function () {
    Route::get('/complete-profile', CollectEmail::class)
        ->name('social.collect-email');
});


Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Dashboard deposit routes
Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/deposit/{deposit}/track', DepositTracker::class)
         ->name('deposit.track');

    Route::get('/deposit/create', CreateDeposit::class)
         ->name('deposit.create');

    Route::get('/subscribe', Subscribe::class)
         ->name('subscribe');

    Route::get('/subscription/{subscription}/track', SubscriptionTracker::class)
        ->name('subscription.track');
});

Route::post('/webhook/nowpayments', [NowPaymentsWebhookController::class, 'handle'])
    ->name('webhook.nowpayments');












// _______________________________________________________________________________________________________________________________
// _______________________________________________________________________________________________________________________________

Route::get('/terminal', Terminal::class)
    ->middleware(['auth', 'verified'])
    ->name('terminal');

// Temporary routes
Route::get('/support', function() {
    Auth::user()->onboarding->markStep('profile_completed');
    return 'support';
})->name('settings.profile');

Route::get('/signals', function() {
    return 'signals';
})->name('signals');

Route::get('/bots', function() {
    return 'bots';
})->name('bots');

Route::get('/notifications', function() {
    return 'settings.notifications';
})->name('settings.notifications');




require __DIR__ . '/auth.php';
require __DIR__ . '/onboarding.php';
