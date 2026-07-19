<?php

return [
    /*
     * Below this score, a deployed formation is forcibly exited — its
     * slots are undeployed and handed back to the auto-deployment sweep
     * on the same run. This is a blunt fixed threshold for now; if tiers
     * ever need differentiated risk tolerance, move this onto PackTier
     * instead of config.
     */
    'exit_score_threshold' => 20,

    /*
     * A formation only accepts NEW deployments if its score is at or
     * above this floor, on top of the existing state check (must be
     * ACTIVE). Keeps a formation that's technically ACTIVE but scoring
     * poorly from absorbing fresh capital right before it decays past
     * exit_score_threshold.
     */
    'min_deployment_score' => 40,

    'min_deployment_score_by_tier' => [
        'scout'    => 40,
        'vanguard' => 50,
        'dominion' => 60,
    ],

    /*
     * Soft concentration cap — auto-deployment won't push a slot into a
     * formation whose deployment_summary()['total_capital'] already
     * exceeds this, even if it's the top-scored option. Prevents one
     * formation absorbing every funded slot in a single sweep. Set to
     * null to disable.
     */
    'max_capital_per_formation' => 50000.0,

    /*
     * Floor for the rolling per-slot earning clock — a slot's next
     * payout is never sooner than this many hours after its last one.
     */
    'earning_min_interval_hours' => 24,

    /*
     * Random jitter added ON TOP of the floor above, in minutes, so
     * slots don't all pay out at the exact same offset forever. A
     * slot's real interval is anywhere from 24h to 24h + this value.
     */
    'earning_jitter_max_minutes' => 180,

    'max_exposure_ratio' => 0.15,
];