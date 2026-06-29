<?php

namespace App\Services;

use App\Enums\PackSlotStatus;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\PackSlot;
use App\Models\SlotEarning;
use Illuminate\Support\Facades\DB;

/**
 * Replaces EarningsEngineService entirely. The key difference: there's no
 * global multiplier anymore. Each FUNDED slot only earns if it's actually
 * DEPLOYED into a formation (formation_id set) — an undeployed slot
 * ("Waiting For Qualification") earns nothing that day, which is the
 * correct behavior per the PDF ("Senflux deploys capital into qualifying
 * formations" — capital sitting unqualified isn't earning).
 */
class DailySlotEarningsService
{
    public function __construct(private WalletService $wallet) {}

    public function processAllFundedSlots(): void
    {
        $today = now()->toDateString();

        PackSlot::where('status', PackSlotStatus::FUNDED->value)
            ->whereNotNull('formation_id')
            ->with(['formation', 'subscription.packTier'])
            ->chunkById(100, function ($slots) use ($today) {
                foreach ($slots as $slot) {
                    $this->processSlotEarning($slot, $today);
                }
            });
    }

    public function processSlotEarning(PackSlot $slot, string $date): ?SlotEarning
    {
        if (SlotEarning::where('pack_slot_id', $slot->id)->where('earned_date', $date)->exists()) {
            return null; // already processed today — idempotent
        }

        $formation = $slot->formation;
        if (!$formation) {
            return null; // not deployed — no earning today
        }

        $tier = $slot->subscription->packTier;
        $baseRate = $tier->baselineDailyRate();
        $multiplier = $formation->state->earningsMultiplier();
        $principal = (float) $slot->capital_amount;

        $earning = round($principal * $baseRate * $multiplier, 8);

        if ($earning <= 0) {
            // Still record the zero-earning day for a complete history,
            // just skip the wallet credit — crediting $0.00000000 would
            // just be noise in the ledger.
            return SlotEarning::create([
                'pack_slot_id' => $slot->id,
                'user_id' => $slot->subscription->user_id,
                'formation_id' => $formation->id,
                'amount' => 0,
                'base_rate_applied' => $baseRate,
                'formation_state' => $formation->state->value,
                'formation_multiplier' => $multiplier,
                'earned_date' => $date,
                'processed_at' => now(),
            ]);
        }

        return DB::transaction(function () use ($slot, $formation, $date, $earning, $baseRate, $multiplier) {
            $tx = $this->wallet->credit(
                user: $slot->subscription->user,
                walletType: WalletType::MAIN,
                amount: $earning,
                type: TransactionType::PACK_SLOT_EARNING,
                description: "Daily earning — slot #{$slot->slot_number} ({$formation->token_symbol}) — {$date}",
                referenceId: $slot->id,
                referenceType: PackSlot::class,
                meta: ['date' => $date, 'formation' => $formation->token_symbol, 'rate' => $baseRate, 'multiplier' => $multiplier],
            );

            $record = SlotEarning::create([
                'pack_slot_id' => $slot->id,
                'user_id' => $slot->subscription->user_id,
                'formation_id' => $formation->id,
                'wallet_transaction_id' => $tx->id,
                'amount' => $earning,
                'base_rate_applied' => $baseRate,
                'formation_state' => $formation->state->value,
                'formation_multiplier' => $multiplier,
                'earned_date' => $date,
                'processed_at' => now(),
            ]);

            $slot->increment('realized_profit', $earning);

            return $record;
        });
    }
}
