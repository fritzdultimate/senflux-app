<?php

namespace App\Livewire\Protected;

use App\Models\Formation;
use App\Models\PackSlot;
use App\Services\FormationDeploymentService;
use App\Services\FormationTimelineService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class FormationDetail extends Component {
    public Formation $formation;
    public int $timelineLimit = 20;
    public bool $showAllDeployedSlots = false;

    public function mount(Formation $formation): void {
        $this->formation = $formation;
    }

    #[Computed]
    public function fresh(): Formation {
        return $this->formation->fresh();
    }

    #[Computed]
    public function eventsTotal(): int {
        return $this->formation->events()->count();
    }

    #[Computed]
    public function timelineGroups(): array {
        $events = $this->formation->events()
            ->latest('created_at')
            ->take($this->timelineLimit)
            ->get();

        return app(FormationTimelineService::class)->group($events);
    }

    #[Computed]
    public function timelineHasMore(): bool {
        return $this->eventsTotal > $this->timelineLimit;
    }

    #[Computed]
    public function recentTrades() {
        return $this->formation->recentTradeActivities(6);
    }

    #[Computed]
    public function deployment(): ?array {
        return $this->formation->userDeploymentStatus(Auth::user());
    }

    public function loadMoreTimeline(): void {
        $this->timelineLimit += 20;
        unset($this->timelineGroups, $this->timelineHasMore);
    }

    public function toggleDeployedSlots(): void {
        $this->showAllDeployedSlots = !$this->showAllDeployedSlots;
    }

    public function deploy(int $slotId, FormationDeploymentService $deploymentService): void {
        $slot = PackSlot::whereHas('subscription', fn ($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($slotId);

        try {
            $deploymentService->deploy($slot, $this->formation);
            session()->flash('status', "Deployed into {$this->formation->token_symbol}.");
            unset($this->deployment);
        } catch (\DomainException $e) {
            $this->addError('deployment', $e->getMessage());
        }
    }

    #[Poll(10000)]
    public function refresh(): void {
        unset($this->fresh, $this->eventsTotal, $this->timelineGroups, $this->timelineHasMore, $this->recentTrades);
    }

    public function render() {
        return view('livewire.protected.formation-detail')
            ->title("\${$this->formation->token_symbol} · Formation Detail");
    }
}