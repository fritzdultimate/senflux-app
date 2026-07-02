<?php
// app/View/Components/FormationCard.php

namespace App\View\Components;

use App\Models\Formation;
use Illuminate\View\Component;

class FormationCard extends Component
{
    public function __construct(public Formation $formation, public bool $readonly = false) {}

    public function render() {
        return view('components.formation-card');
    }
}