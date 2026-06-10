<?php

namespace App\Livewire\Onboarding;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Welcome to Senflux')]
class Welcome extends Component {
    public function mount(): void {
        $onboarding = Auth::user()->onboarding;

        // Already seen welcome — go to dashboard
        if ($onboarding->welcome_dismissed) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function dismiss(): void {
        Auth::user()->onboarding->update(['welcome_dismissed' => true]);
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render(): \Illuminate\View\View {
        return view('livewire.onboarding.welcome', [
            'user' => Auth::user(),
        ]);
    }
}