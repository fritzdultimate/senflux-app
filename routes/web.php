<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
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

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');


Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/auth.php';
