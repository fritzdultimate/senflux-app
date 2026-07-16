<?php

namespace App\Filament\Resources\Formations\Schemas;

use App\Enums\FormationState;
use App\Models\Formation;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')->schema([
                    TextInput::make('token_symbol')->required()->maxLength(20),
                    TextInput::make('token_name')->required()->maxLength(100),
                    TextInput::make('ecosystem')->default('Solana')->required(),
                    TextInput::make('mint_address')
                        ->label('Solana Mint Address')
                        ->helperText('Required for on-chain verification. Leave blank if this formation has no real token yet — the Terminal will not show a "Verify" link without one.')
                        ->maxLength(64),
                    Select::make('sector')
                        ->options([
                            'memecoins' => 'Memecoins', 'ai_agents' => 'AI Agents', 'defi' => 'DeFi',
                            'depin' => 'DePIN', 'gaming' => 'Gaming', 'rwa' => 'RWA',
                            'nft' => 'NFT', 'infrastructure' => 'Infrastructure',
                        ])
                        ->native(false)
                        ->helperText('Drives the Participation Heatmap grouping — leave blank to exclude from it.'),
                ])->columns(3),

                Section::make('Formation Status')->schema([
                    Select::make('state')
                        ->options(collect(FormationState::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                        ->required()
                        ->native(false)
                        ->helperText('Changing this fires a FormationEvent automatically and updates the earnings multiplier applied to any deployed slot.'),
                    TextInput::make('score')->numeric()->minValue(0)->maxValue(100)->required(),
                    Select::make('confidence')
                        ->options(['Low' => 'Low', 'Moderate' => 'Moderate', 'High' => 'High'])
                        ->required()->native(false),
                    Toggle::make('is_active')->default(true)
                        ->helperText('Inactive formations stop appearing in the Terminal feed entirely.'),
                ])->columns(4),

                Section::make('Intelligence Metrics')->schema([
                    TextInput::make('capital_concentration')->numeric()->suffix('%')->minValue(0)->maxValue(100)->required(),
                    TextInput::make('liquidity_migration')
                        ->numeric()
                        ->suffix('%')
                        ->minValue(0)
                        ->maxValue(100)
                        ->required()
                        ->disabled(fn (?Formation $record) => $record?->hasComputedLiquidityMigration() ?? false)
                        ->helperText(fn (?Formation $record) => $record?->hasComputedLiquidityMigration()
                            ? 'Auto-computed from real 24h liquidity trend — no longer manually editable.'
                            : 'Manual until 24h of on-chain liquidity history is available, then auto-computed.'),
                    TextInput::make('participation_growth')->numeric()->suffix('%')->minValue(0)->maxValue(100)->required(),
                    TextInput::make('wallet_quality')->numeric()->suffix('%')->minValue(0)->maxValue(100)->required(),
                ])->columns(4),

                Section::make('Market Data (DexScreener)')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        TextInput::make('dex')->maxLength(30),
                        TextInput::make('pair_address')->maxLength(64),
                        TextInput::make('pair_url')->url()->columnSpan(2),
                        TextInput::make('price_usd')->numeric()->prefix('$'),
                        TextInput::make('liquidity_usd')->numeric()->prefix('$'),
                        TextInput::make('volume_24h')->numeric()->prefix('$'),
                        TextInput::make('fdv')->numeric()->prefix('$'),
                        TextInput::make('market_cap')->numeric()->prefix('$'),
                        TextInput::make('price_change_24h')->numeric()->suffix('%'),
                        TextInput::make('buys_24h')->numeric(),
                        TextInput::make('sells_24h')->numeric(),
                        DateTimePicker::make('market_data_synced_at')->disabled()->dehydrated(false),
                    ]),

                Section::make('Settings')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')->label('Active (visible / deployable candidate)')->default(true),
                    Toggle::make('auto_managed')->label('Auto-Managed')
                        ->helperText('If on, the detection/scoring engine updates this formation automatically. Turn off to freeze it for manual admin control.'),
                    Textarea::make('notes')->columnSpanFull()->rows(3),
                ]),
            ]);
    }
}
