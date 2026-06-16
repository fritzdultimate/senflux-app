<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_configs', function (Blueprint $table) {
            $table->id();
            $table->string('plan', 20)->unique();          // enum PlanType value
            $table->string('label', 60);
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('quarterly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2);
            $table->decimal('daily_rate_min', 6, 4);       // e.g. 0.0060
            $table->decimal('daily_rate_max', 6, 4);
            $table->decimal('min_deposit', 15, 2)->default(100);
            $table->decimal('max_deposit', 15, 2)->default(999999);
            $table->json('features')->nullable();           // feature bullet list
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default plans
        DB::table('plan_configs')->insert([
            [
                'plan'            => 'core',
                'label'           => 'Core',
                'monthly_price'   => 20.00,
                'quarterly_price' => 59.00,
                'yearly_price'    => 199.00,
                'daily_rate_min'  => 0.0060,
                'daily_rate_max'  => 0.0060,
                'min_deposit'     => 100.00,
                'max_deposit'     => 5000.00,
                'features'        => json_encode([
                    'Real-time formation visibility',
                    'Standard participation monitoring',
                    'Delayed intelligence intervals',
                    'Structured allocation environment',
                    'Limited concurrent deployment capacity',
                ]),
                'is_active'   => true,
                'is_popular'  => false,
                'sort_order'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'plan'            => 'pro',
                'label'           => 'Pro',
                'monthly_price'   => 50.00,
                'quarterly_price' => 139.00,
                'yearly_price'    => 499.00,
                'daily_rate_min'  => 0.0090,
                'daily_rate_max'  => 0.0090,
                'min_deposit'     => 5000.00,
                'max_deposit'     => 15000.00,
                'features'        => json_encode([
                    'Accelerated formation updates',
                    'Expanded participation coverage',
                    'Higher deployment flexibility',
                    'Enhanced execution responsiveness',
                    'Multi-formation monitoring',
                ]),
                'is_active'   => true,
                'is_popular'  => true,
                'sort_order'  => 2,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'plan'            => 'apex',
                'label'           => 'Apex',
                'monthly_price'   => 100.00,
                'quarterly_price' => 269.00,
                'yearly_price'    => 699.00,
                'daily_rate_min'  => 0.0130,
                'daily_rate_max'  => 0.0130,
                'min_deposit'     => 15000.00,
                'max_deposit'     => 9999999.00,
                'features'        => json_encode([
                    'Full participation intelligence layer',
                    'Highest execution priority',
                    'Dynamic allocation architecture',
                    'Advanced formation visibility',
                    'Maximum deployment capacity',
                ]),
                'is_active'   => true,
                'is_popular'  => false,
                'sort_order'  => 3,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_configs');
    }
};
