<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\FormationShareController;
use App\Http\Controllers\Webhook\NowPaymentsWebhookController;
use App\Livewire\Auth\CollectEmail;
use App\Livewire\Pages\About;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\HowItWorks;
use App\Livewire\Pages\MarketInsights;
use App\Livewire\Protected\Affiliate;
use App\Livewire\Protected\Alerts;
use App\Livewire\Protected\Dashboard;
use App\Livewire\Protected\Deposit\CreateDeposit;
use App\Livewire\Protected\Deposit\DepositTracker;
use App\Livewire\Protected\LiveTrades;
use App\Livewire\Protected\Markets;
use App\Livewire\Protected\MyBots;
use App\Livewire\Protected\Packs\BrowsePacks;
use App\Livewire\Protected\Packs\MyPacks;
use App\Livewire\Protected\Packs\SubscriptionDetail;
use App\Livewire\Protected\Portfolio;
use App\Livewire\Protected\RankRewards;
use App\Livewire\Protected\Settings;
use App\Livewire\Protected\Signals;
use App\Livewire\Protected\Subscription\Subscribe;
use App\Livewire\Protected\Subscription\SubscriptionTracker;
use App\Livewire\Protected\Terminal;
use App\Livewire\Protected\TradingBots;
use App\Livewire\Protected\Wallet;
use App\Livewire\Protected\Withdraw;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    // Route::get('/subscription/{subscription}/track', SubscriptionTracker::class)
    //     ->name('subscription.track');

    Route::get('/packs', MyPacks::class)->name('packs.index');
    Route::get('/packs/browse', BrowsePacks::class)->name('packs.browse');
    Route::get('/packs/{subscription}', SubscriptionDetail::class)->name('packs.show');

    Route::get('/withdraw', Withdraw::class)
    ->name('withdraw');

    Route::get('/affiliate', Affiliate::class)
        ->name('affiliate');

    Route::get('/wallet', Wallet::class)
        ->name('wallet');

    Route::get('/rank-rewards', RankRewards::class)
        ->name('rank-rewards');

    Route::get('/portfolio', Portfolio::class)
        ->name('portfolio');

    Route::get('/markets', Markets::class)
        ->name('markets');

    Route::get('/alerts', Alerts::class)
        ->name('alerts');

    Route::get('/settings', Settings::class)
        ->name('settings');

    Route::get('/bots', TradingBots::class)
        ->name('bots');

    Route::get('/bots/mine', MyBots::class)
        ->name('bots.mine');

    Route::get('/live-trades', LiveTrades::class)
        ->name('live-trades');

    Route::get('/signals', Signals::class)
        ->name('signals');

    Route::get('/terminal', Terminal::class)
        ->name('terminal');

    Route::get('/market-insights', \App\Livewire\Protected\MarketInsights::class)
        ->name('market-insights');
});

Route::post('/webhook/nowpayments', [NowPaymentsWebhookController::class, 'handle'])
    ->name('webhook.nowpayments');

Route::get('/test-log', function () {
    Log::error('Telescope should capture this');
    return 'ok';
});











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

Route::get('/f/{formation}', [FormationShareController::class, 'show'])->name('formations.share');
Route::get('/f/{formation}/og.png', [FormationShareController::class, 'ogImage'])->name('formations.share.og');


Route::get('/dev/market-data-test', function (\App\Services\MarketData\CachedMarketDataService $service) {
    return view('dev.market-data-test', ['data' => $service->summarizeAll()]);
});




require __DIR__ . '/auth.php';
require __DIR__ . '/onboarding.php';
require __DIR__ . '/cronjob.php';
