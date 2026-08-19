<?php

namespace App\Livewire\Auth;

use App\Models\Formation;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter as FacadesRateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Sign In — Senflux')]
class Login extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $showPassword = false;

    protected function rules(): array {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    protected function messages(): array {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Enter a valid email address.',
            'password.required' => 'Password is required.',
        ];
    }

    #[Computed]
    public function activeWallets() {
        $currentActiveWallets = Cache::remember(
            'active_wallets_today',
            now()->endOfDay(), // expires at end of today
            function () {
                return Formation::active()
                    ->whereIn('state', [
                        'active',
                        'matured',
                    ])
                    ->sum('active_wallets');
            }
        );

        return $currentActiveWallets;
    }

    #[Computed]
    public function percentageIncrease() {
        $yesterdayActiveWallets = Cache::get('active_wallets_yesterday', 0);
        $increase = 0;

        if ($yesterdayActiveWallets > 0) {
            $increase = (
                ($this->activeWallets - $yesterdayActiveWallets)
                / $yesterdayActiveWallets
            ) * 100;
        }

        $increase = number_format($increase);

        return "+{$increase}%";
    }

    public function updated(string $field): void {
        $this->validateOnly($field);
    }

    // ── Rate limit key ────────────────────────────────────────────
    private function throttleKey(): string {
        return Str::transliterate(
            Str::lower($this->email) . '|' . request()->ip()
        );
    }

    // ── Ensure not locked out ─────────────────────────────────────
    private function ensureNotRateLimited(): void {
        if (! FacadesRateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = FacadesRateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    // ── Login ─────────────────────────────────────────────────────
    public function login(): void {
        $this->validate();
        $this->ensureNotRateLimited();

        if (! Auth::attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            FacadesRateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        FacadesRateLimiter::clear($this->throttleKey());

        $user = Auth::user();

        // ── Email must be verified ────────────────────────────────
        if (! $user->hasVerifiedEmail()) {
            $this->redirect(route('verification.notice'), navigate: true);
            return;
        }

        // ── 2FA check — only a fully confirmed enrollment gates login ──
        if ($user->two_factor_enable && $user->two_factor_secret && $user->two_factor_confirmed_at) {
            // Store user id in session, force 2FA challenge
            session(['2fa_user_id' => $user->id]);
            Auth::logout();

            $this->redirect(route('two-factor.challenge'), navigate: true);
            return;
        }

        session()->regenerate();
        // $this->redirect(route('dashboard'), navigate: true);

        $onboarding = $user->onboarding;
        $destination = $onboarding->welcome_dismissed
            ? route('dashboard')
            : route('welcome');

        $this->redirect($destination, navigate: true);
    }

    public function render(): \Illuminate\View\View {
        return view('livewire.auth.login');
    }
}