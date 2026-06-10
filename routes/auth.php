<?php

// ─────────────────────────────────────────────────────────────────────────────
// Auth routes  →  add to routes/web.php
// ─────────────────────────────────────────────────────────────────────────────

use App\Livewire\Auth\EmailVerification;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\TwoFactor;
use Illuminate\Support\Facades\Route;

// ── Guest-only routes ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {

    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');

    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');

    Route::get('/two-factor-challenge', TwoFactor::class)->name('two-factor.challenge');

});

// ── Auth-only: email verification ────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/email/verify', EmailVerification::class)
        ->name('verification.notice');

    // Laravel's signed verification URL handler (built-in controller)
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard');
    })->middleware('signed')->name('verification.verify');

});

// ── Logout ───────────────────────────────────────────────────────────────────
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');