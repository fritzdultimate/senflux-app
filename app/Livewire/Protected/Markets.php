<?php

namespace App\Livewire\Protected;

use App\Models\Formation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Markets')]
class Markets extends Component
{
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function formation()
    {
        return Formation::where('is_active', true)
            ->first();
    }

    #[Computed]
    public function formationHistory()
    {
        return Formation::orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function planConfigs()
    {
        return DB::table('plan_configs')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function platformStats(): array
    {
        return [
            'total_deposited' => (float) DB::table('deposits')
                ->whereIn('status', ['active', 'finished'])
                ->sum('actually_paid_usd'),
            'active_deposits' => DB::table('deposits')->where('status', 'active')->count(),
            'total_users'     => DB::table('users')->count(),
            'total_paid_out'  => (float) DB::table('wallet_transactions')
                ->where('type', 'withdrawal')
                ->sum('amount'),
        ];
    }

    #[Poll(30000)]
    public function refresh(): void
    {
        unset($this->formation, $this->formationHistory, $this->platformStats);
    }

    public function render()
    {
        return view('livewire.protected.markets');
    }
}
