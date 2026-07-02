<?php
// app/Livewire/Terminal/ActivityTicker.php

namespace App\Livewire\Terminal;

use App\Models\FormationEvent;
use Livewire\Component;

class ActivityTicker extends Component {
    public function render() {
        return view('livewire.terminal.activity-ticker', [
            'events' => FormationEvent::with('formation')->recent(15)->get(),
        ]);
    }
}