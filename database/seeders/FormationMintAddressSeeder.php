<?php
// database/seeders/FormationMintAddressSeeder.php

namespace Database\Seeders;

use App\Models\Formation;
use Illuminate\Database\Seeder;

class FormationMintAddressSeeder extends Seeder
{
    public function run(): void
    {
        $mints = [
            'WIF'    => 'EKpQGSJtjMFqKZ9KQanSqYXRcF8fBopzLHYxdM65zcjm',
            'BONK'   => 'DezXAZ8z7PnrnRJjz3wXBoRgixCa6xjnB7YaB1pPB263',
            'POPCAT' => '7GCihgDB8fe6KNjn2MYtkzZcRjQy3t9GHdC8uHYmW2hr',
            'JTO'    => 'jtojtomepa8beP8AuQc6eXt5FriJwfFMwQx2v2f9mCL',
            'PYTH'   => 'HZ1JovNiVvGrGNiiYvEozEVgZ58xaU3RKwX8eACQBCt3',
        ];

        foreach ($mints as $symbol => $mint) {
            Formation::where('token_symbol', $symbol)->update(['mint_address' => $mint]);
        }
    }
}