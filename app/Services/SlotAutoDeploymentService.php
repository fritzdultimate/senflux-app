<?php

namespace App\Services;

use App\Enums\FormationEventType;
use App\Enums\PackSlotStatus;
use App\Enums\PackSubscriptionStatus;
use App\Models\Formation;
use App\Models\PackSlot;

/**
 * Scheduler-driven — replaces the admin's manual formation pick from
 * FormationDeploymentService::deploy() with an automatic best-match.
 * FormationDeploymentService itself is untouched; this is just the
 * "which formation" decision layered on top of it.
 */
class SlotAutoDeploymentService {
    public function __construct(
        private FormationDeploymentService $deployer,
        private FormationEventLogger $eventLogger,
    ) {}

    /**
     * Run every cycle (piggyback on the same 5-min cron that runs
     * FormationAutoDetectionService/FormationScoringService, so slots
     * get matched against freshly-scored formations, not stale ones).
     *
     * Returns how many slots were deployed this pass.
     */
    public function deployEligibleSlots(): int {
        $deployedCount = 0;

        $slots = PackSlot::where('status', PackSlotStatus::FUNDED->value)
            ->whereNull('formation_id')
            ->whereHas('subscription', fn ($q) => $q->where('status', PackSubscriptionStatus::ACTIVE->value))
            ->with('subscription.packTier')
            ->get();

        if ($slots->isEmpty()) {
            return 0;
        }

        // Track capital committed to a formation WITHIN this pass, so we
        // don't blow past max_capital_per_formation by deploying five
        // slots into the same top-scored formation in one sweep.
        $committedThisPass = [];

        foreach ($slots as $slot) {
            $formation = $this->pickFormationFor($slot, $committedThisPass);

            if (!$formation) {
                continue;
            }

            $this->deployer->deploy($slot, $formation);

            $committedThisPass[$formation->id] = ($committedThisPass[$formation->id] ?? 0)
                + (float) $slot->capital_amount;

            $deployedCount++;
        }

        return $deployedCount;
    }

    private function pickFormationFor(PackSlot $slot, array $committedThisPass): ?Formation {
        $minScore = config('packs.min_deployment_score', 40);
        $capPerFormation = config('packs.max_capital_per_formation');

        $candidates = Formation::acceptingDeployments()
            ->where('score', '>=', $minScore)
            ->orderByDesc('score')
            ->get();

        foreach ($candidates as $formation) {
            if ($capPerFormation === null) {
                return $formation;
            }

            $existingCapital = (float) $formation->deploymentSummary()['total_capital'];
            $pendingCapital = $committedThisPass[$formation->id] ?? 0.0;
            $projected = $existingCapital + $pendingCapital + (float) $slot->capital_amount;

            if ($projected <= $capPerFormation) {
                return $formation;
            }
        }

        return null;
    }
}