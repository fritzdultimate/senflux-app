<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller {
    // ── Supported providers ───────────────────────────────────────
    private array $providers = ['google', 'github', 'facebook'];

    // ── Redirect to provider ──────────────────────────────────────
    public function redirect(string $provider) {
        abort_unless(in_array($provider, $this->providers), 404);

        return Socialite::driver($provider)->redirect();
    }

    // ── Handle callback ───────────────────────────────────────────
    public function callback(string $provider) {
        abort_unless(in_array($provider, $this->providers), 404);

        try {
            $social = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Authentication failed. Please try again.']);
        }

        $user = $this->findOrCreateUser($social, $provider);

        // Blocked or suspended
        if ($user->blocked_at || $user->suspended_at) {
            return redirect()->route('login')
                ->withErrors(['email' => 'This account has been suspended.']);
        }

        Auth::login($user, remember: true);
        session()->regenerate();

        // 2FA check
        if ($user->two_factor_enable && $user->two_factor_secret) {
            session(['2fa_user_id' => $user->id]);
            Auth::logout();
            return redirect()->route('two-factor.challenge');
        }

        // Email collection needed
        if ($user->needs_email) {
            return redirect()->route('social.collect-email');
        }

        // return redirect()->intended(route('dashboard'));

        $destination = $user->onboarding->welcome_dismissed
            ? route('dashboard')
            : route('welcome');

        return redirect()->intended($destination);
    }

    // ── Find or create user ───────────────────────────────────────
    private function findOrCreateUser($social, string $provider): User {
        // 1. Match by provider + provider_id (returning social user)
        $existing = User::where('provider', $provider)
            ->where('provider_id', $social->getId())
            ->first();

        if ($existing) {
            // Keep avatar fresh
            $existing->updateQuietly(['avatar' => $social->getAvatar()]);
            return $existing;
        }

        // 2. Match by email (user registered with email, now using social)
        //    Link the social provider to their existing account
        if ($social->getEmail()) {
            $byEmail = User::where('email', $social->getEmail())->first();

            if ($byEmail) {
                $byEmail->updateQuietly([
                    'provider' => $provider,
                    'provider_id' => $social->getId(),
                    'avatar' => $social->getAvatar(),
                    // Mark email verified since provider already verified it
                    'email_verified_at' => $byEmail->email_verified_at ?? now(),
                ]);
                return $byEmail;
            }
        }

        // 3. New user — create account
        $name = $social->getName() ?? '';
        $parts = explode(' ', trim($name), 2);
        $firstname = $parts[0] ?? 'User';
        $lastname = $parts[1] ?? '';

        $base = Str::slug($firstname . $lastname) ?: 'user';
        $username = $this->uniqueUsername($base);

        $email      = $social->getEmail();
        $needsEmail = false;

        if (! $email) {
            $email = $provider . '_' . $social->getId() . '@placeholder.senflux.io';
            $needsEmail = true;
        }

        return User::create([
            'name' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => Hash::make(Str::random(32)), // unusable password
            'provider' => $provider,
            'provider_id' => $social->getId(),
            'avatar' => $social->getAvatar(),
            'affiliate_code' => $this->generateAffiliateCode(),
            'email_verified_at' => now(),
            'needs_email' => $needsEmail
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────
    private function uniqueUsername(string $base): string {
        $username = $base;
        $i = 1;

        while (User::where('name', $username)->exists()) {
            $username = $base . $i++;
        }

        return $username;
    }

    private function generateAffiliateCode(): string {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('affiliate_code', $code)->exists());

        return $code;
    }
}