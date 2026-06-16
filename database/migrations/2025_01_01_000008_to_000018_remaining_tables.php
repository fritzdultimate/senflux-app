<?php
// FILE: 2025_01_01_000008_create_referrals_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('level')->default(1); // direct = 1
            $table->timestamps();
            $table->index('referrer_id');
        });

        Schema::create('referral_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('earner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('source_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('deposit_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('level');                  // 1–8
            $table->decimal('rate', 5, 4);                 // 0.08, 0.04, etc.
            $table->decimal('amount', 15, 8);
            $table->foreignId('wallet_transaction_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index('earner_id');
            $table->index('deposit_id');
        });

        Schema::create('rank_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('rank', 30)->unique();           // enum RankLevel value
            $table->string('label', 60);
            $table->decimal('team_volume_usd', 18, 2);
            $table->decimal('personal_deposit_usd', 15, 2);
            $table->unsignedInteger('direct_referrals');
            $table->decimal('cash_bonus', 15, 2);
            $table->decimal('leadership_match_rate', 5, 4)->default(0.15);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed from enum values
        $ranks = [
            ['rank' => 'genesis',   'label' => 'Genesis',   'team_volume_usd' => 5000,       'personal_deposit_usd' => 500,    'direct_referrals' => 2,  'cash_bonus' => 150,     'sort_order' => 1],
            ['rank' => 'pioneer',   'label' => 'Pioneer',   'team_volume_usd' => 15000,      'personal_deposit_usd' => 1000,   'direct_referrals' => 3,  'cash_bonus' => 500,     'sort_order' => 2],
            ['rank' => 'vanguard',  'label' => 'Vanguard',  'team_volume_usd' => 50000,      'personal_deposit_usd' => 2000,   'direct_referrals' => 5,  'cash_bonus' => 1500,    'sort_order' => 3],
            ['rank' => 'prime',     'label' => 'Prime',     'team_volume_usd' => 150000,     'personal_deposit_usd' => 5000,   'direct_referrals' => 6,  'cash_bonus' => 5000,    'sort_order' => 4],
            ['rank' => 'empire',    'label' => 'Empire',    'team_volume_usd' => 500000,     'personal_deposit_usd' => 10000,  'direct_referrals' => 8,  'cash_bonus' => 10000,   'sort_order' => 5],
            ['rank' => 'horizon',   'label' => 'Horizon',   'team_volume_usd' => 1000000,    'personal_deposit_usd' => 15000,  'direct_referrals' => 10, 'cash_bonus' => 50000,   'sort_order' => 6],
            ['rank' => 'monarch',   'label' => 'Monarch',   'team_volume_usd' => 2500000,    'personal_deposit_usd' => 25000,  'direct_referrals' => 12, 'cash_bonus' => 100000,  'sort_order' => 7],
            ['rank' => 'apex',      'label' => 'Apex',      'team_volume_usd' => 5000000,    'personal_deposit_usd' => 50000,  'direct_referrals' => 15, 'cash_bonus' => 250000,  'sort_order' => 8],
            ['rank' => 'crown',     'label' => 'Crown',     'team_volume_usd' => 10000000,   'personal_deposit_usd' => 100000, 'direct_referrals' => 20, 'cash_bonus' => 500000,  'sort_order' => 9],
            ['rank' => 'sovereign', 'label' => 'Sovereign', 'team_volume_usd' => 25000000,   'personal_deposit_usd' => 150000, 'direct_referrals' => 25, 'cash_bonus' => 1000000, 'sort_order' => 10],
        ];

        foreach ($ranks as &$r) {
            $r['leadership_match_rate'] = 0.15;
            $r['is_active']  = true;
            $r['created_at'] = now();
            $r['updated_at'] = now();
        }

        \DB::table('rank_requirements')->insert($ranks);

        Schema::create('rank_advancements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('from_rank', 30);
            $table->string('to_rank', 30);
            $table->decimal('bonus_amount', 15, 2);
            $table->foreignId('wallet_transaction_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->timestamp('achieved_at');
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('leadership_match_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('earner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('source_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rank_advancement_id')->constrained()->cascadeOnDelete();
            $table->decimal('rate', 5, 4)->default(0.15);
            $table->decimal('amount', 15, 2);
            $table->foreignId('wallet_transaction_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index('earner_id');
        });

        Schema::create('team_volumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('level_1', 18, 2)->default(0);
            $table->decimal('level_2', 18, 2)->default(0);
            $table->decimal('level_3', 18, 2)->default(0);
            $table->decimal('level_4', 18, 2)->default(0);
            $table->decimal('level_5', 18, 2)->default(0);
            $table->decimal('level_6', 18, 2)->default(0);
            $table->decimal('level_7', 18, 2)->default(0);
            $table->decimal('level_8', 18, 2)->default(0);
            $table->decimal('raw_total', 18, 2)->default(0);      // sum levels 1-8 unweighted
            $table->decimal('weighted_total', 18, 2)->default(0); // after distribution %
            $table->timestamp('last_computed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 15, 8);
            $table->decimal('fee', 15, 8)->default(0);
            $table->decimal('net_amount', 15, 8);
            $table->string('wallet_address', 200);
            $table->string('network', 50);
            $table->string('crypto_currency', 20);
            $table->string('status', 20)->default('pending'); // enum WithdrawalStatus
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('tx_hash', 200)->nullable();
            $table->foreignId('wallet_transaction_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('status');
        });

        Schema::create('market_formation_states', function (Blueprint $table) {
            $table->id();
            $table->string('state', 20);                   // enum MarketFormationState
            $table->string('ecosystem', 50)->default('solana');
            $table->string('bot_status', 20)->default('standby'); // enum BotDeploymentStatus
            $table->unsignedInteger('active_wallets')->nullable();
            $table->decimal('liquidity_score', 5, 2)->nullable();
            $table->decimal('participation_score', 5, 2)->nullable();
            $table->decimal('formation_score', 5, 2)->nullable();
            $table->decimal('earnings_multiplier', 5, 4)->default(1.0);
            $table->text('notes')->nullable();
            $table->foreignId('set_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->index('is_current');
        });

        // Seed initial state
        \DB::table('market_formation_states')->insert([
            'state'               => 'building',
            'ecosystem'           => 'solana',
            'bot_status'          => 'deployed',
            'earnings_multiplier' => 0.9,
            'is_current'          => true,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        Schema::create('withdrawal_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_amount', 15, 2)->default(10);
            $table->decimal('max_amount', 15, 2)->default(50000);
            $table->string('fee_type', 20)->default('percentage'); // percentage|flat
            $table->decimal('fee_value', 8, 4)->default(0);
            $table->unsignedInteger('processing_days')->default(1);
            $table->json('allowed_networks')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        \DB::table('withdrawal_settings')->insert([
            'min_amount'      => 10.00,
            'max_amount'      => 50000.00,
            'fee_type'        => 'percentage',
            'fee_value'       => 0,
            'processing_days' => 1,
            'allowed_networks'=> json_encode(['sol', 'bsc', 'eth', 'trc20']),
            'is_enabled'      => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string|integer|decimal|boolean|json
            $table->string('group', 50)->default('general');
            $table->string('label', 100);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('group');
        });

        $settings = [
            ['key' => 'site_name',             'value' => 'Senflux',        'type' => 'string',  'group' => 'general',      'label' => 'Site Name'],
            ['key' => 'site_tagline',           'value' => 'Markets Leave Footprints Before They Move', 'type' => 'string', 'group' => 'general', 'label' => 'Site Tagline'],
            ['key' => 'maintenance_mode',       'value' => '0',              'type' => 'boolean', 'group' => 'general',      'label' => 'Maintenance Mode'],
            ['key' => 'deposits_enabled',       'value' => '1',              'type' => 'boolean', 'group' => 'deposits',     'label' => 'Deposits Enabled'],
            ['key' => 'withdrawals_enabled',    'value' => '1',              'type' => 'boolean', 'group' => 'withdrawals',  'label' => 'Withdrawals Enabled'],
            ['key' => 'referral_enabled',       'value' => '1',              'type' => 'boolean', 'group' => 'referral',     'label' => 'Referral System Enabled'],
            ['key' => 'earnings_enabled',       'value' => '1',              'type' => 'boolean', 'group' => 'earnings',     'label' => 'Daily Earnings Enabled'],
            ['key' => 'nowpayments_api_key',    'value' => '',               'type' => 'string',  'group' => 'payments',     'label' => 'NowPayments API Key'],
            ['key' => 'nowpayments_ipn_secret', 'value' => '',               'type' => 'string',  'group' => 'payments',     'label' => 'NowPayments IPN Secret'],
            ['key' => 'nowpayments_sandbox',    'value' => '1',              'type' => 'boolean', 'group' => 'payments',     'label' => 'NowPayments Sandbox Mode'],
            ['key' => 'support_email',          'value' => 'support@senflux.ai', 'type' => 'string', 'group' => 'general',  'label' => 'Support Email'],
            ['key' => 'telegram_link',          'value' => '',               'type' => 'string',  'group' => 'social',       'label' => 'Telegram Link'],
            ['key' => 'twitter_link',           'value' => '',               'type' => 'string',  'group' => 'social',       'label' => 'Twitter/X Link'],
        ];

        foreach ($settings as &$s) {
            $s['created_at'] = now();
            $s['updated_at'] = now();
        }

        \DB::table('platform_settings')->insert($settings);

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->text('description')->nullable();
            $table->string('subject_type', 80)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
        });
    }
    public function down(): void { 
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('referral_bonuses');
        Schema::dropIfExists('rank_requirements');
        Schema::dropIfExists('rank_advancements');
        Schema::dropIfExists('leadership_match_bonuses');
        Schema::dropIfExists('team_volumes');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('market_formation_states');
        Schema::dropIfExists('withdrawal_settings');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('activity_logs');
    }
};
