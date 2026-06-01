<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('About Us')]
class About extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.about');
    }
}
