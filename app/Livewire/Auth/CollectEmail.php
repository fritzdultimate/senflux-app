<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Complete Your Profile — Senflux')]
class CollectEmail extends Component {
    public string $email = '';

    public function mount(): void {
        if (! Auth::user()->needs_email) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    protected function rules(): array {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ];
    }

    protected function messages(): array {
        return [
            'email.unique' => 'This email is already registered to another account.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }

    public function updated(string $field): void {
        $this->validateOnly($field);
    }

    public function save(): void {
        $this->validate();

        $user = Auth::user();

        $user->forceFill([
            'email' => $this->email,
            'email_verified_at'  => null,
            'needs_email' => false,
        ])->save();

        // Send verification email
        $user->sendEmailVerificationNotification();

        $this->redirect(route('verification.notice'), navigate: true);
    }

    public function render(): \Illuminate\View\View {
        return view('livewire.auth.collect-email');
    }
}