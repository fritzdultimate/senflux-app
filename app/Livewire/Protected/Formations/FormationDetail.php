<?php

namespace App\Livewire\Protected\Formations;

use App\Models\Formation;
use App\Models\PackSlot;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class FormationDetail extends Component
{
    #[Locked]
    public int $formationId;

    public function mount(Formation $formation): void
    {
        $this->formationId = $formation->id;
    }

    #[Computed]
    public function formation(): Formation
    {
        return Formation::find($this->formationId);
    }

    /**
     * The PDF's lifecycle timeline: Detected -> Building -> Validated ->
     * Deployment Started -> Currently Active. Mapped from the same
     * FormationState a card already shows — Validated/Deployment Started
     * both correspond to ACTIVE (the moment a formation both validates
     * AND starts accepting capital), since the spec doesn't distinguish
     * "just validated" from "first deployment" as separate states.
     */
    #[Computed]
    public function timelineSteps(): array
    {
        $state = $this->formation->state->value;
        $order = ['idle' => 0, 'early' => 1, 'building' => 2, 'active' => 3, 'mature' => 4, 'weakening' => 3];
        $current = $order[$state] ?? 0;

        $steps = [
            ['label' => 'Detected', 'at' => 0],
            ['label' => 'Building', 'at' => 2],
            ['label' => 'Validated', 'at' => 3],
            ['label' => 'Deployment Started', 'at' => 3],
            ['label' => 'Currently Active', 'at' => 3],
        ];

        foreach ($steps as &$step) {
            $step['completed'] = $current > $step['at'];
            $step['active'] = $current === $step['at'];
        }

        return $steps;
    }

    /**
     * My own slots and their deployment status against THIS formation —
     * the "Eligible For Deployment / Already Deployed / Waiting For
     * Qualification" panel from the spec.
     */
    #[Computed]
    public function mySlots()
    {
        return PackSlot::whereHas('subscription', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('status', 'funded')
            ->get();
    }

    public function render()
    {
        return view('livewire.protected.formations.formation-detail');
    }
}
