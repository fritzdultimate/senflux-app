<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Livewire\Pages\About;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\HowItWorks;
use App\Livewire\Pages\MarketInsights;
use App\Livewire\Pages\Terminal;
use App\Livewire\Protected\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/',               Home::class)->name('home');
Route::get('/about',          About::class)->name('about');
Route::get('/terminal',       Terminal::class)->name('terminal');
Route::get('/how-it-works',   HowItWorks::class)->name('how-it-works');
Route::get('/market-insights',MarketInsights::class)->name('market-insights');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback');


Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/auth.php';
