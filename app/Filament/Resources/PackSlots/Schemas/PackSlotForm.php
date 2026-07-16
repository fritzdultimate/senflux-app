<?php

namespace App\Filament\Resources\PackSlots\Schemas;

use App\Enums\PackSlotStatus;
use App\Models\PackSubscription;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackSlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Slot')
                ->columns(3)
                ->schema([
                    Select::make('pack_subscription_id')
                        ->label('Subscription')
                        ->relationship('subscription', 'id')
                        ->getOptionLabelFromRecordUsing(fn (PackSubscription $r) => "#{$r->id} — {$r->user?->name} ({$r->packTier?->name})")
                        ->disabled() // moving a slot to a different subscription is not a supported operation
                        ->dehydrated(false),
                    TextInput::make('slot_number')->numeric()->disabled()->dehydrated(false),
                    Select::make('status')
                        ->options(collect(PackSlotStatus::cases())->mapWithKeys(
                            fn (PackSlotStatus $s) => [$s->value => ucfirst($s->value)]
                        ))
                        ->required()
                        ->helperText('Manual status override — use with care, this bypasses the normal fund/deploy/exit flow and its side effects (wallet transactions, referral bonuses).'),
                ]),
 
            Section::make('Deployment')
                ->columns(2)
                ->schema([
                    Select::make('formation_id')
                        ->relationship('formation', 'token_symbol')
                        ->searchable()
                        ->preload()
                        ->helperText('Changing this directly does NOT log a FormationEvent or run FormationDeploymentService — prefer using the app\'s deploy/undeploy actions where possible.'),
                    DateTimePicker::make('deployed_at'),
                    DateTimePicker::make('next_earning_at')
                        ->helperText('The slot\'s next eligible payout time — see DailySlotEarningsService.'),
                ]),
 
            Section::make('Capital — locked by default')
                ->description('These fields drive real wallet balances and ledger history. Toggle "Unlock" only if you\'re certain a correction is needed and you understand it will NOT create a matching WalletTransaction automatically.')
                ->columns(3)
                ->schema([
                    Toggle::make('unlock_financials')
                        ->label('Unlock financial fields')
                        ->dehydrated(false)
                        ->live()
                        ->columnSpanFull(),
                    TextInput::make('capital_amount')->numeric()->prefix('$')
                        ->disabled(fn ($get) => ! $get('unlock_financials')),
                    TextInput::make('realized_profit')->numeric()->prefix('$')
                        ->disabled(fn ($get) => ! $get('unlock_financials')),
                    TextInput::make('early_exit_fee_charged')->numeric()->prefix('$')
                        ->disabled(fn ($get) => ! $get('unlock_financials')),
                    Toggle::make('was_early_exit')
                        ->disabled(fn ($get) => ! $get('unlock_financials')),
                    DateTimePicker::make('funded_at')
                        ->disabled(fn ($get) => ! $get('unlock_financials')),
                    DateTimePicker::make('closed_at')
                        ->disabled(fn ($get) => ! $get('unlock_financials')),
                ]),
            ]);
    }
}
