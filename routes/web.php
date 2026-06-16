<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Livewire\Auth\CollectEmail;
use App\Livewire\Pages\About;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\HowItWorks;
use App\Livewire\Pages\MarketInsights;
use App\Livewire\Protected\Dashboard;
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

Route::get('/termina', Terminal::class)
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
