<?php

namespace App\Filament\Resources\PackSubscriptions\Schemas;

use App\Enums\PackSubscriptionStatus;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription')
                ->columns(3)
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->getOptionLabelFromRecordUsing(fn (User $r) => "{$r->name} ({$r->email})")
                        ->disabled()
                        ->dehydrated(false),
                    Select::make('pack_tier_id')
                        ->relationship('packTier', 'name')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Use a real-time or renewal-window upgrade in the app to change tier — not this form.'),
                    Select::make('status')
                        ->options(collect(PackSubscriptionStatus::cases())->mapWithKeys(
                            fn (PackSubscriptionStatus $s) => [$s->value => ucfirst(str_replace('_', ' ', $s->value))]
                        ))
                        ->required()
                        ->helperText('Manual override — bypasses PackLifecycleService\'s normal transitions and their side effects.'),
                ]),
 
            Section::make('Timeline')
                ->columns(3)
                ->schema([
                    DateTimePicker::make('purchased_at')->disabled()->dehydrated(false),
                    DateTimePicker::make('matures_at'),
                    DateTimePicker::make('renewal_window_ends_at'),
                    DateTimePicker::make('upgraded_at')->disabled()->dehydrated(false),
                    DateTimePicker::make('refunded_at')->disabled()->dehydrated(false),
                ]),
            ]);
    }
}
