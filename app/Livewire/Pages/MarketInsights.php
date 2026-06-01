<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Market Insights')]
class MarketInsights extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.market-insights');
    }
}
