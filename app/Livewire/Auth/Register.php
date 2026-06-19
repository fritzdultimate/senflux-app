<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Create Your Account — Senflux')]
class Register extends Component
{
    // ── Form fields ───────────────────────────────────────────────
    public string $firstname   = '';
    public string $lastname    = '';
    public string $email       = '';
    public string $refcode     = '';
    public string $password    = '';
    public string $password_confirmation = '';
    public string $plan = 'free';
    public bool $terms = false;
    public bool $marketing = true;

    // ── UI state ──────────────────────────────────────────────────
    public bool $showPassword = false;
    public bool   $showPasswordConfirm = false;

    public function mount() {
        $ref = request()->query('ref');

        if ($ref && User::where('affiliate_code', $ref)->exists()) {
            $this->refcode = $ref;
        }
    }

    // ── Validation rules ──────────────────────────────────────────
    protected function rules(): array {
        return [
            'firstname' => ['required', 'string', 'min:2', 'max:50'],
            'lastname' => ['required', 'string', 'min:2', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'refcode' => ['nullable', 'string', 'exists:users,affiliate_code'],
            'password' => ['required', Password::min(8)->numbers()->symbols(), 'confirmed'],
            'terms' => ['accepted'],
        ];
    }

    protected function messages(): array {
        return [
            'firstname.required' => 'First name is required.',
            'firstname.min' => 'First name must be at least 2 characters.',
            'lastname.required' => 'Last name is required.',
            'lastname.min' => 'Last name must be at least 2 characters.',
            'email.unique' => 'This email is already registered.',
            'email.email' => 'Please enter a valid email address.',
            'refcode.exists' => 'That referral code does not exist.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'terms.accepted' => 'You must accept the Terms of Service.',
        ];
    }

    // ── Real-time validation (fires on blur) ──────────────────────
    public function updated(string $field): void {
        $this->validateOnly($field);
    }

    // ── Plan selection ────────────────────────────────────────────
    public function selectPlan(string $plan): void {
        $this->plan = in_array($plan, ['free', 'pro']) ? $plan : 'free';
    }

    // ── Submit ────────────────────────────────────────────────────
    public function register(): void {
        $this->validate();

        $baseUsername = Str::slug($this->firstname . $this->lastname);
        $username = $this->uniqueUsername($baseUsername);

        $referrer = $this->refcode
            ? User::where('affiliate_code', strtoupper($this->refcode))->first()
            : null;

        $user = User::create([
            'name' => $username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'vpss' => $this->password,
            'affiliate_code' => $this->generateAffiliateCode(),
            'referrer_id' => $referrer?->id,
            'notify_email_notifications' => $this->marketing,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('verification.notice'), navigate: true);
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

    // ── Render ────────────────────────────────────────────────────
    public function render(): \Illuminate\View\View {
        return view('livewire.auth.register');
    }
}