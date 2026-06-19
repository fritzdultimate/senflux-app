<?php

namespace App\Livewire\Protected;

use App\Models\Referral;
use App\Models\ReferralBonus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.protected')]
#[Title('Affiliate')]
class Affiliate extends Component
{
    use WithPagination;

    #[Url]
    public string $levelFilter = 'all';

    public string $copiedFlash = '';

    // Bonus rate per level — mirrors ReferralBonusService::RATES
    public array $rates = [
        1 => 0.08,
        2 => 0.04,
        3 => 0.025,
        4 => 0.02,
        5 => 0.015,
        6 => 0.0125,
        7 => 0.01,
        8 => 0.0075,
    ];

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function referralLink(): string {
        return rtrim(config('app.url'), '/') . '/register?ref=' . $this->user->affiliate_code;
    }

    #[Computed]
    public function directReferralsCount(): int
    {
        return DB::table('referrals')->where('referrer_id', $this->user->id)->count();
    }

    #[Computed]
    public function totalNetworkSize(): int
    {
        return DB::table('referrals')->where('referrer_id', $this->user->id)->count()
            + $this->levelBreakdown()->sum('count') - $this->directReferralsCount();
    }

    #[Computed]
    public function totalReferralEarnings(): float
    {
        return (float) ReferralBonus::where('earner_id', $this->user->id)->sum('amount');
    }

    #[Computed]
    public function thisMonthEarnings(): float
    {
        return (float) ReferralBonus::where('earner_id', $this->user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    #[Computed]
    public function levelBreakdown()
    {
        // Count of network members + bonus earned, grouped by level (1-8)
        $counts = DB::table('referrals')
            ->select('level', DB::raw('count(*) as count'))
            ->where('referrer_id', $this->user->id)
            ->groupBy('level')
            ->pluck('count', 'level');

        $earnings = ReferralBonus::where('earner_id', $this->user->id)
            ->selectRaw('level, SUM(amount) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        return collect(range(1, 8))->map(fn($level) => [
            'level'   => $level,
            'rate'    => $this->rates[$level] ?? 0,
            'count'   => (int) ($counts[$level] ?? 0),
            'earned'  => (float) ($earnings[$level] ?? 0),
        ]);
    }

    #[Computed]
    public function directReferrals()
    {
        $query = $this->user->referrals()
            ->select('id', 'name', 'email', 'created_at', 'subscription_plan')
            ->withSum(['deposits as total_deposited' => fn($q) =>
                $q->whereIn('status', ['active', 'finished'])
            ], 'actually_paid_usd')
            ->latest();

        return $query->paginate(8);
    }

    #[Computed]
    public function recentBonuses()
    {
        return ReferralBonus::where('earner_id', $this->user->id)
            ->with('sourceUser:id,name')
            ->latest()
            ->take(8)
            ->get();
    }

    public function flashCopied(): void
    {
        $this->copiedFlash = 'Link copied to clipboard!';
    }

    public function render()
    {
        return view('livewire.protected.affiliate'); 
    }
}
