<?php

namespace App\Livewire\Onboarding;

use App\Models\OnboardingProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Checklist extends Component {
    public bool $collapsed = false;

    public function mount(): void {
        $this->collapsed = Auth::user()->onboarding->progressPercent() >= 60;
    }

    public function skip(): void {
        Auth::user()->onboarding->update([
            'completed'    => true,
            'completed_at' => now(),
        ]);
    }

    #[On('onboarding:step-complete')]
    public function markStep(string $step): void {
        Auth::user()->onboarding->markStep($step);
    }

    public function render(): \Illuminate\View\View {
        $onboarding = Auth::user()->onboarding;

        return view('livewire.onboarding.checklist', [
            'onboarding' => $onboarding,
            'steps' => OnboardingProgress::steps(),
            'percent' => $onboarding->progressPercent(),
            'done' => $onboarding->completedSteps(),
            'total' => $onboarding->totalSteps(),
        ]);
    }
}