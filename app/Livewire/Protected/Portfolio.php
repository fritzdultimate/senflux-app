<?php

namespace App\Livewire\Protected;

use App\Enums\PackSlotStatus;
use App\Enums\PackSubscriptionStatus;
use App\Models\PackSubscription;
use App\Models\SlotEarning;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Portfolio')]
class Portfolio extends Component
{
    #[Url]
    public string $range = '30'; // days: 7, 30, 90, 'all'

    public ?int $selectedSubscriptionId = null;

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function allSubscriptions()
    {
        // Excludes REFUNDED — refunded subscriptions never actually
        // deployed capital, so they shouldn't count toward the portfolio.
        return $this->user->packSubscriptions()
            ->where('status', '!=', PackSubscriptionStatus::REFUNDED)
            ->with(['packTier', 'slots.formation'])
            ->latest('purchased_at')
            ->get();
    }

    #[Computed]
    public function totalPrincipal(): float
    {
        return (float) $this->allSubscriptions
            ->flatMap->slots
            ->where('status', '!=', PackSlotStatus::EMPTY)
            ->sum('capital_amount');
    }

    #[Computed]
    public function totalEarned(): float
    {
        return (float) SlotEarning::where('user_id', $this->user->id)->sum('amount');
    }

    #[Computed]
    public function portfolioValue(): float
    {
        return $this->totalPrincipal + $this->totalEarned;
    }

    #[Computed]
    public function roiPercent(): float
    {
        return $this->totalPrincipal > 0
            ? round(($this->totalEarned / $this->totalPrincipal) * 100, 2)
            : 0;
    }

    #[Computed]
    public function activeSubscriptionsCount(): int
    {
        return $this->allSubscriptions->where('status', PackSubscriptionStatus::ACTIVE)->count();
    }

    #[Computed]
    public function activeSlotsCount(): int
    {
        return $this->allSubscriptions
            ->flatMap->slots
            ->where('status', PackSlotStatus::FUNDED)
            ->count();
    }

    #[Computed]
    public function rangeDays(): int
    {
        return match ($this->range) {
            '7'   => 7,
            '30'  => 30,
            '90'  => 90,
            'all' => (int) ($this->allSubscriptions->min('purchased_at')?->diffInDays(now()) ?? 30),
            default => 30,
        };
    }

    #[Computed]
    public function earningsChart(): array
    {
        $days = max(1, min($this->rangeDays, 365));
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = SlotEarning::where('user_id', $this->user->id)
            ->where('earned_date', '>=', $start->toDateString())
            ->selectRaw('earned_date, SUM(amount) as total')
            ->groupBy('earned_date')
            ->pluck('total', 'earned_date');

        $points = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $points->push([
                'date'  => $date,
                'label' => now()->parse($date)->format($days > 31 ? 'M j' : 'D'),
                'total' => (float) ($rows[$date] ?? 0),
            ]);
        }

        return $points->toArray();
    }

    #[Computed]
    public function cumulativeChart(): array
    {
        $running = 0;
        return collect($this->earningsChart)->map(function ($point) use (&$running) {
            $running += $point['total'];
            return [...$point, 'cumulative' => $running];
        })->toArray();
    }

    #[Computed]
    public function packBreakdown(): array
    {
        return $this->allSubscriptions->map(function (PackSubscription $sub) {
            $fundedSlots = $sub->slots->where('status', '!=', PackSlotStatus::EMPTY);
            $principal   = (float) $fundedSlots->sum('capital_amount');
            $earned      = (float) SlotEarning::where('user_id', $this->user->id)
                ->whereIn('pack_slot_id', $sub->slots->pluck('id'))
                ->sum('amount');

            return [
                'id'            => $sub->id,
                'tier'          => $sub->packTier->name,
                'status'        => $sub->status->value,
                'status_label'  => $sub->status->label(),
                'principal'     => $principal,
                'earned'        => $earned,
                'roi_pct'       => $principal > 0 ? round(($earned / $principal) * 100, 2) : 0,
                'daily_rate'    => $sub->packTier->baselineDailyRate(),
                'purchased_at'  => $sub->purchased_at,
                'matures_at'    => $sub->matures_at,
                'days_active'   => $sub->purchased_at?->diffInDays(now()) ?? 0,
                'slots_funded'  => $fundedSlots->count(),
                'slots_total'   => $sub->slots->count(),
                'slots'         => $sub->slots->map(fn ($slot) => [
                    'slot_number'     => $slot->slot_number,
                    'status'          => $slot->status->value,
                    'status_label'    => $slot->status->label(),
                    'capital_amount'  => (float) $slot->capital_amount,
                    'realized_profit' => (float) $slot->realized_profit,
                    'formation_symbol'=> $slot->formation?->token_symbol,
                ])->toArray(),
            ];
        })->toArray();
    }

    #[Computed]
    public function selectedSubscriptionEarnings()
    {
        if (!$this->selectedSubscriptionId) {
            return collect();
        }

        $slotIds = optional(
            $this->allSubscriptions->firstWhere('id', $this->selectedSubscriptionId)
        )->slots->pluck('id') ?? collect();

        return SlotEarning::whereIn('pack_slot_id', $slotIds)
            ->where('user_id', $this->user->id)
            ->latest('earned_date')
            ->take(30)
            ->get();
    }

    public function selectSubscription(?int $id): void
    {
        $this->selectedSubscriptionId = $this->selectedSubscriptionId === $id ? null : $id;
    }

    public function setRange(string $range): void
    {
        $this->range = $range;
    }

    public function render()
    {
        return view('livewire.protected.portfolio');
    }
}