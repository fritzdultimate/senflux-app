<?php

namespace App\Livewire\Protected;

use App\Enums\TradeActivitySource;
use App\Models\Formation;
use App\Models\FormationTradeActivity;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.protected')]
#[Title('Live Trades')]
class LiveTradesPage extends Component {
    use WithPagination;

    #[Url(as: 'formation')]
    public ?int $formationId = null;

    #[Url]
    public ?string $source = null; // 'market_pool' | 'senflux' | null = all

    #[Url]
    public ?string $type = null; // 'buy' | 'sell' | null = all — confirm actual values with Emeka

    #[Url]
    public bool $includeFailed = false;

    #[Computed]
    public function formation(): ?Formation {
        return $this->formationId ? Formation::find($this->formationId) : null;
    }

    #[Computed]
    public function trades() {
        return FormationTradeActivity::with('formation')
            ->where('token_amount', '>', 0)
            ->where('source', TradeActivitySource::SENFLUX)
            ->when($this->formationId, fn ($q) => $q->where('formation_id', $this->formationId))
            ->when($this->source, fn ($q) => $q->where('source', $this->source))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->unless($this->includeFailed, fn ($q) => $q->where('failed', false))
            ->latest('block_time')
            ->paginate(30);
    }

    #[Computed]
    public function stats(): array {
        $base = FormationTradeActivity::query()
            ->when($this->formationId, fn ($q) => $q->where('formation_id', $this->formationId))
            ->where('block_time', '>=', now()->subDay());

        return [
            'trades_24h' => (clone $base)->where('failed', false)->count(),
            'buys_24h' => (clone $base)->where('failed', false)->where('type', 'buy')->count(),
            'sells_24h' => (clone $base)->where('failed', false)->where('type', 'sell')->count(),
            'failed_24h' => (clone $base)->where('failed', true)->count(),
            'active_formations' => (clone $base)->where('failed', false)->distinct('formation_id')->count('formation_id'),
        ];
    }

    public function filterByFormation(?int $id): void {
        $this->formationId = $id;
        $this->resetPage();
        unset($this->trades, $this->stats, $this->formation);
    }

    public function filterBySource(?string $source): void {
        $this->source = $source;
        $this->resetPage();
        unset($this->trades, $this->stats);
    }

    public function filterByType(?string $type): void {
        $this->type = $type;
        $this->resetPage();
        unset($this->trades, $this->stats);
    }

    public function toggleFailed(): void {
        $this->includeFailed = !$this->includeFailed;
        $this->resetPage();
        unset($this->trades);
    }

    #[Poll(8000)]
    public function refresh(): void {
        unset($this->trades, $this->stats);
    }

    public function render() {
        return view('livewire.protected.live-trades-page');
    }
}