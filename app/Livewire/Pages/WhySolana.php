<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Why Solana')]
class WhySolana extends Component {
    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.why-solana');
    }
}
