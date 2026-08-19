<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\TeamVolume;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeamVolumeService
{
    // Team volume distribution weights per level
    private const WEIGHTS = [
        1 => 1.00,
        2 => 0.75,
        3 => 0.50,
        4 => 0.25,
        5 => 0.15,
        6 => 0.10,
        7 => 0.05,
        8 => 0.025,
    ];

    /**
     * Compute and cache team volume for a user.
     */
    public function computeForUser(User $user): TeamVolume {
        $levels = [];
        $currentLevel = [$user->id];
        $visited = [$user->id => true];

        for ($level = 1; $level <= 8; $level++) {
            $nextLevel = [];
            $levelVolume = 0;

            foreach ($currentLevel as $uid) {
                $directReferrals = DB::table('referrals')
                    ->where('referrer_id', $uid)
                    ->pluck('referred_id')
                    ->toArray();

                foreach ($directReferrals as $rid) {
                    if (isset($visited[$rid])) continue;
                    $visited[$rid] = true;
                    $nextLevel[] = $rid;

                    // Sum active deposit volumes for this person
                    $vol = Deposit::where('user_id', $rid)
                        ->whereIn('status', ['active', 'finished'])
                        ->sum('actually_paid_usd');

                    $levelVolume += (float) $vol;
                }
            }

            $levels[$level] = $levelVolume;
            $currentLevel = $nextLevel;

            if (empty($currentLevel)) break;
        }

        $rawTotal = array_sum($levels);
        $weightedTotal = 0;
        foreach ($levels as $l => $vol) {
            $weightedTotal += $vol * (self::WEIGHTS[$l] ?? 0);
        }

        $record = TeamVolume::updateOrCreate(
            ['user_id' => $user->id],
            [
                'level_1' => $levels[1] ?? 0,
                'level_2' => $levels[2] ?? 0,
                'level_3' => $levels[3] ?? 0,
                'level_4' => $levels[4] ?? 0,
                'level_5' => $levels[5] ?? 0,
                'level_6' => $levels[6] ?? 0,
                'level_7' => $levels[7] ?? 0,
                'level_8' => $levels[8] ?? 0,
                'raw_total' => $rawTotal,
                'weighted_total' => $weightedTotal,
                'last_computed_at' => now(),
            ]
        );

        return $record;
    }

    /**
     * Get direct referral count (level 1 only).
     */
    public function getDirectReferralCount(User $user): int
    {
        return DB::table('referrals')->where('referrer_id', $user->id)->count();
    }

    /**
     * Get total personal deposit volume for a user.
     */
    public function getPersonalDepositVolume(User $user): float
    {
        return (float) Deposit::where('user_id', $user->id)
            ->whereIn('status', ['active', 'finished'])
            ->sum('actually_paid_usd');
    }
}
