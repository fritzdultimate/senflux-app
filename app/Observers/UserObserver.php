<?php

namespace App\Observers;

use App\Models\OnboardingProgress;
use App\Models\User;

class UserObserver
{
    /**
     * Auto-create onboarding record when a new user registers.
     * This ensures ->onboarding never returns null.
     */
    public function created(User $user): void {
        OnboardingProgress::create(['user_id' => $user->id]);
    }
}


// ─────────────────────────────────────────────────────────────────────────────
// Register in AppServiceProvider::boot():
// ─────────────────────────────────────────────────────────────────────────────
//
// use App\Models\User;
// use App\Observers\UserObserver;
//
// User::observe(UserObserver::class);