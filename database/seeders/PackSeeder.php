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
        $packs = [
            [
                'name' => 'scout',
                'price' => 250,
                'duration' => 7,
                'slot' => 3,
                'min_amount' => 100
            ],
            [
                'name' => 'vanguard',
                'price' => 500,
                'duration' => 14,
                'slot' => 5,
                'min_amount' => 500
            ],
            [
                'name' => 'dominion',
                'price' => 1000,
                'duration' => 21,
                'slot' => 10,
                'min_amount' => 1000
            ]
        ];


        foreach ($packs as $pack) {
            PackTier::create([
                'key' => $pack['name'],
                'name' => ucfirst($pack['name']),
                'price' => $pack['price'],
                'duration_days' => $pack['duration'],
                'slot_count' => $pack['slot'],
                'min_capital_per_slot' => $pack['min_amount']
            ]);
        }

        $this->command->info("Seeded packs.");
    }
}
