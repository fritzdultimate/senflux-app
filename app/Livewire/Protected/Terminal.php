<?php
// app/Livewire/Protected/Terminal.php

namespace App\Livewire\Protected;

use App\Models\Formation;
use App\Models\FormationEvent;
use App\Models\PackSlot;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.protected')]
class Terminal extends Component {
    use WithPagination;
    public ?int $activeFormationId = null;
    public bool $showAllDeployedSlots = false;

    #[Computed]
    public function formations() {
        return Formation::active()->orderByDesc('score')->paginate(10);
    }

    #[Computed]
    public function activityEvents() {
        return FormationEvent::with('formation')->recent(15)->get();
    }


    #[Computed]
    public function sectorHeatmap(): array {
        $sectors = [
            'memecoins' => 'Memecoins', 'ai_agents' => 'AI Agents', 'defi' => 'DeFi',
            'depin' => 'DePIN', 'gaming' => 'Gaming', 'rwa' => 'RWA',
            'nft' => 'NFT', 'infrastructure' => 'Infrastructure',
        ];

        return collect($sectors)->map(function ($label, $key) {
            $avg = Formation::active()->where('sector', $key)->avg('score');

            return [
                'label' => $label,
                'strength' => $avg === null ? 'idle' : match (true) {
                    $avg >= 80 => 'vs', $avg >= 60 => 's', $avg >= 40 => 'm', $avg >= 20 => 'e', default => 'w',
                },
                'strengthLabel' => $avg === null ? 'Idle' : match (true) {
                    $avg >= 80 => 'Very Strong', $avg >= 60 => 'Strong', $avg >= 40 => 'Moderate', $avg >= 20 => 'Early', default => 'Weak',
                },
            ];
        })->values()->toArray();
    }

    #[Computed]
    public function platformStats(): array {
        return [
            'active_participants' => PackSlot::whereNotNull('formation_id')->distinct('pack_subscription_id')->count('pack_subscription_id'),
            'new_deployments_24h' => PackSlot::whereNotNull('formation_id')->where('deployed_at', '>=', now()->subDay())->count(),
            'capital_deployed' => PackSlot::whereNotNull('formation_id')->sum('capital_amount'),
            'avg_formation_score' => round(Formation::active()->avg('score') ?? 0),
        ];
    }

    #[Poll(10000)]
    public function refresh(): void {
        unset($this->formations, $this->activityEvents);
    }

    public function render() {
        return view('livewire.protected.terminal'); 
    }
}