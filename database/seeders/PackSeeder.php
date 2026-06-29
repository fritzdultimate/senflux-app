<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Group;
use App\Models\PackTier;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Run manually with: php artisan db:seed --class=Database\\Seeders\\DemoCompetitionSeeder
 *
 * Creates one small 4-team single-group competition so the schema/relationships
 * (Competition -> Group -> competition_team -> Team) can be checked in Tinker
 * or a quick query before the fixture-generation engine (Phase 3) exists.
 */
class PackSeeder extends Seeder {
    public function run(): void {
        $features = [
            'scout' => [
                'Flexible access for smaller capital allocations',
                'Shorter participation period',
                'Standard Formation Access',
                'Live Formation Feed',
                'Real-Time Deployment Tracking',
                'Capital Intelligence Dashboard',
                'Daily Profit Withdrawals'
            ],
            'vanguard' => [ 
                'Broader exposure across multiple formations', 
                'Longer participation period', 
                'Enhanced Formation Coverage', 
                'Advanced Wallet Intelligence', 
                'Capital Rotation Monitoring', 
                'Formation Strength Analytics', 
                'Daily Profit Withdrawals'
            ],
            'dominion' => [
                'Maximum exposure to capital rotation opportunities', 
                'Long-term formation development', 
                'Advanced Capital Intelligence Coverage', 
                'Full Formation Monitoring Access', 
                'Sector Rotation Intelligence', 
                'Multi-Wallet Cluster Analysis', 
                'Institutional Analytics Dashboard'
            ]
        ];


        foreach ($features as $key => $feature) {
            PackTier::where('key', $key)->update([
                'features' => json_encode($feature)
            ]);

        }

        $this->command->info("Seeded packs.");
    }
}
