<?php

use App\Livewire\Onboarding\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', Welcome::class)
    ->middleware(['auth', 'verified'])
    ->name('welcome');



Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    $user = $request->user();
    $onboarding = $user->onboarding;

    return $onboarding->welcome_dismissed
        ? redirect()->route('dashboard')
        : redirect()->route('welcome');

})->middleware(['auth', 'signed'])->name('verification.verify');
