<?php

namespace App\Livewire\Protected;

use App\Enums\TradeActivitySource;
use App\Models\Formation;
use App\Models\FormationTradeActivity;
use App\Models\PackSlot;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.protected')]
#[Title('Bot Activity')]
class LiveTradesPage extends Component {
    use WithPagination;

    #[Url(as: 'formation')]
    public ?int $formationId = null;

    #[Url]
    public ?string $source = null; // 'market_pool' | 'senflux' | null = all

    #[Url]
    public ?string $type = null; // 'buy' | 'sell' | null = all

    #[Url]
    public bool $includeFailed = false;

    #[Url]
    public string $tab = 'activity'; // 'activity' (narrative feed) | 'history' (raw table)

    public function switchTab(string $tab): void {
        $this->tab = in_array($tab, ['activity', 'history'], true) ? $tab : 'activity';
    }

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

    /**
     * "What your bot is doing" — narrative activity feed.
     *
     * ASSUMPTION: FormationTradeActivity has no stored decision reason
     * today. Long-term, SlotAutoDeploymentService / FormationScoringService
     * should persist the real scoring reason at execution time (e.g. a
     * `decision_reason` string column) so this feed reflects actual
     * intelligence output instead of the type+state fallback below.
     */
    #[Computed]
    public function activityFeed() {
        $trades = FormationTradeActivity::with('formation')
            ->where('token_amount', '>', 0)
            ->where('source', TradeActivitySource::SENFLUX)
            ->where('failed', false)
            ->when($this->formationId, fn ($q) => $q->where('formation_id', $this->formationId))
            ->latest('block_time')
            ->limit(40)
            ->get();

        $max = $trades->max('token_amount') ?: 1;

        return $trades->map(fn ($trade) => [
            'trade' => $trade,
            'title' => $trade->type === 'buy' ? 'Capital Deployed' : 'Position Reduced',
            'reason' => $this->narrativeReason($trade),
            // relative size of this action against the largest in the current
            // feed window — powers the impact bar on each card
            'impact_pct' => max(6, (int) round(($trade->token_amount / $max) * 100)),
        ]);
    }

    private function narrativeReason(FormationTradeActivity $trade): string {
        $state = $trade->formation?->state?->value ?? null;

        return match (true) {
            $trade->type === 'buy' && $state === 'building' => 'Participation strengthening — formation building momentum',
            $trade->type === 'buy' => 'Qualifying conditions met — formation validated for deployment',
            $trade->type === 'sell' && $state === 'weakening' => 'Formation strength weakening',
            $trade->type === 'sell' => 'Qualifying conditions deteriorated',
            default => 'Formation conditions changed',
        };
    }

    #[Computed]
    public function botStatus(): array {
        $lastTrade = FormationTradeActivity::where('source', TradeActivitySource::SENFLUX)
            ->latest('block_time')
            ->first();

        return [
            // ASSUMPTION: no real heartbeat source wired up yet — wire this
            // to a queue/cron last-run health check if you want it to mean
            // something beyond "the page rendered".
            'active' => true,
            'last_activity' => $lastTrade?->block_time,
        ];
    }

    #[Computed]
    public function overview(): array {
        $today = FormationTradeActivity::query()
            ->where('token_amount', '>', 0)
            ->where('source', TradeActivitySource::SENFLUX)
            ->where('block_time', '>=', Carbon::today());

        $activeSlots = PackSlot::where('status', 'active');

        return [
            'active_formations' => Formation::where('state', 'active')->count(),
            'actions_today' => (clone $today)->count(),
            'successful_today' => (clone $today)->where('failed', false)->count(),
            'failed_today' => (clone $today)->where('failed', true)->count(),
            'capital_deployed' => (clone $activeSlots)->sum('capital_amount'),
            'active_deployments' => (clone $activeSlots)->count(),
        ];
    }

    /**
     * Current Intelligence — formation state breakdown.
     *
     * ASSUMPTION: Formation::state is the six-state lifecycle enum
     * (Idle, Early, Building, Active, Mature, Weakening). "Strengthening"
     * and "Stable" aren't literal states in that enum, so they're mapped
     * here as a first pass: Early → strengthening, Building/Active →
     * building, Mature → stable, Weakening → weakening. Confirm this
     * mapping matches how you want it described before shipping.
     */
    #[Computed]
    public function intelligence(): array {
        $counts = Formation::query()
            ->selectRaw('state, count(*) as c')
            ->groupBy('state')
            ->get()
            ->mapWithKeys(fn ($row) => [
                (is_object($row->state) ? $row->state->value : $row->state) => $row->c,
            ]);

        $total = $counts->sum();
        $strengthening = $counts->get('early', 0);
        $building = $counts->get('building', 0) + $counts->get('active', 0);
        $stable = $counts->get('mature', 0);
        $weakening = $counts->get('weakening', 0);

        return [
            'total' => $total,
            'strengthening' => $strengthening,
            'building' => $building,
            'stable' => $stable,
            'weakening' => $weakening,
            // pre-computed ring stops for the conic-gradient radar — degrees
            // around a 360° circle, running in the order the legend lists them
            'ring' => $total > 0 ? [
                'strengthening_deg' => round($strengthening / $total * 360, 1),
                'building_deg' => round(($strengthening + $building) / $total * 360, 1),
                'stable_deg' => round(($strengthening + $building + $stable) / $total * 360, 1),
            ] : ['strengthening_deg' => 0, 'building_deg' => 0, 'stable_deg' => 0],
        ];
    }

    /**
     * Deployment performance.
     *
     * ASSUMPTION: realized/unrealized P/L and 24h % require your
     * SlotEarning schema, which isn't confirmed here — left as explicit
     * TODOs rather than fabricated numbers on a page showing client capital.
     */
    #[Computed]
    public function performance(): array {
        $overview = $this->overview;

        return [
            'active_capital' => $overview['capital_deployed'],
            'realized_profit' => 0, // TODO: wire to SlotEarning realized-profit sum
            'unrealized_pl' => 0,   // TODO: wire to mark-to-market calc
            'change_24h_pct' => 0,  // TODO: wire to 24h performance calc
            'total_actions' => FormationTradeActivity::where('source', TradeActivitySource::SENFLUX)
                ->where('failed', false)
                ->count(),
        ];
    }

    public function filterByFormation(?int $id): void {
        $this->formationId = $id;
        $this->resetPage();
        unset($this->trades, $this->overview, $this->activityFeed, $this->formation);
    }

    public function filterBySource(?string $source): void {
        $this->source = $source;
        $this->resetPage();
        unset($this->trades, $this->overview);
    }

    public function filterByType(?string $type): void {
        $this->type = $type;
        $this->resetPage();
        unset($this->trades, $this->overview);
    }

    public function toggleFailed(): void {
        $this->includeFailed = !$this->includeFailed;
        $this->resetPage();
        unset($this->trades);
    }

    #[Poll(8000)]
    public function refresh(): void {
        unset($this->trades, $this->overview, $this->activityFeed, $this->botStatus);
    }

    public function render() {
        return view('livewire.protected.live-trades-page');
    }
}