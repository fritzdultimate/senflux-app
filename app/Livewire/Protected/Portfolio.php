<?php

namespace App\Livewire\Protected;

use App\Enums\DepositStatus;
use App\Models\Deposit;
use App\Models\DepositEarning;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public ?int $selectedDepositId = null;

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function allDeposits()
    {
        return $this->user->deposits()
            ->whereIn('status', [DepositStatus::ACTIVE->value, DepositStatus::FINISHED->value])
            ->with('planConfig')
            ->latest('activated_at')
            ->get();
    }

    #[Computed]
    public function totalPrincipal(): float
    {
        return (float) $this->allDeposits->sum(fn($d) => $d->actually_paid_usd ?? $d->amount_usd);
    }

    #[Computed]
    public function totalEarned(): float
    {
        return (float) $this->allDeposits->sum('total_earnings');
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
    public function activeCount(): int
    {
        return $this->allDeposits->where('status', DepositStatus::ACTIVE)->count();
    }

    #[Computed]
    public function rangeDays(): int
    {
        return match ($this->range) {
            '7'   => 7,
            '30'  => 30,
            '90'  => 90,
            'all' => (int) ($this->allDeposits->min('activated_at')?->diffInDays(now()) ?? 30),
            default => 30,
        };
    }

    #[Computed]
    public function earningsChart(): array
    {
        $days = max(1, min($this->rangeDays, 365));
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = DepositEarning::where('user_id', $this->user->id)
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
    public function depositBreakdown(): array
    {
        return $this->allDeposits->map(function (Deposit $d) {
            $principal = (float) ($d->actually_paid_usd ?? $d->amount_usd);
            $earned    = (float) $d->total_earnings;

            return [
                'id'        => $d->id,
                'plan'      => $d->planConfig->label,
                'status'    => $d->status,
                'principal' => $principal,
                'earned'    => $earned,
                'roi_pct'   => $principal > 0 ? round(($earned / $principal) * 100, 2) : 0,
                'daily_rate'=> (float) $d->daily_rate,
                'activated' => $d->activated_at,
                'days_active' => $d->activated_at?->diffInDays(now()) ?? 0,
            ];
        })->toArray();
    }

    #[Computed]
    public function selectedDepositEarnings()
    {
        if (!$this->selectedDepositId) return collect();

        return DepositEarning::where('deposit_id', $this->selectedDepositId)
            ->where('user_id', $this->user->id)
            ->latest('earned_date')
            ->take(30)
            ->get();
    }

    public function selectDeposit(?int $id): void
    {
        $this->selectedDepositId = $this->selectedDepositId === $id ? null : $id;
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
