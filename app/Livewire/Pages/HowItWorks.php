<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('How It Works')]
class HowItWorks extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.how-it-works');
    }
}
