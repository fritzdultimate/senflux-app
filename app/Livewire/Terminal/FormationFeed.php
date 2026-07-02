<?php
// app/Livewire/Terminal/FormationFeed.php

namespace App\Livewire\Terminal;

use App\Models\Formation;
use Livewire\Component;

class FormationFeed extends Component {
    public ?int $activeFormationId = null;

    public function openFormation(int $id): void {
        $this->activeFormationId = $id;
    }

    public function closeFormation(): void {
        $this->activeFormationId = null;
    }

    public function render() {
        return view('livewire.terminal.formation-feed', [
            'formations' => Formation::active()->orderByDesc('score')->get(),
        ]);
    }
}