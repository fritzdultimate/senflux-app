<?php
// app/Livewire/Terminal/FormationDetail.php

namespace App\Livewire\Terminal;

use App\Models\Formation;
use Livewire\Component;

class FormationDetail extends Component
{
    public int $formationId;

    public function mount(int $formationId): void {
        $this->formationId = $formationId;
    }

    public function render() {
        $formation = Formation::with('events')
            ->findOrFail($this->formationId);

        return view('livewire.terminal.formation-detail', [
            'formation' => $formation,
            'timeline'  => $formation->events()->oldest('created_at')->get(),
            'deployment' => $formation->userDeploymentStatus(auth()->user()),
        ]);
    }
}