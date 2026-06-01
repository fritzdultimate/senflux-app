<?php

use App\Livewire\Pages\About;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\HowItWorks;
use App\Livewire\Pages\MarketInsights;
use App\Livewire\Pages\Terminal;
use Illuminate\Support\Facades\Route;

Route::get('/',               Home::class)->name('home');
Route::get('/about',          About::class)->name('about');
Route::get('/terminal',       Terminal::class)->name('terminal');
Route::get('/how-it-works',   HowItWorks::class)->name('how-it-works');
Route::get('/market-insights',MarketInsights::class)->name('market-insights');

Route::get('/login',    fn() => redirect('/') )->name('login');
Route::get('/register', fn() => redirect('/') )->name('register');
