<?php

namespace App\Filament\Resources\PackTiers\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackTierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                ->columns(3)
                ->schema([
                    TextInput::make('key')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->helperText('Internal identifier, e.g. "scout". Not shown to users.'),
                    TextInput::make('name')->required()->maxLength(50),
                    TextInput::make('sort_order')->numeric()->default(0)
                        ->helperText('Lower number = shown first on Browse Packs.'),
                ]),
 
            Section::make('Pricing & Structure')
                ->columns(3)
                ->schema([
                    TextInput::make('price')->numeric()->prefix('$')->required(),
                    TextInput::make('duration_days')->numeric()->suffix('days')->required(),
                    TextInput::make('slot_count')->numeric()->required(),
                    TextInput::make('min_capital_per_slot')->numeric()->prefix('$')->required(),
                    TextInput::make('max_capital_per_slot')->numeric()->prefix('$')
                        ->helperText('Leave blank for no upper bound (e.g. Dominion: "$25,000+").'),
                ]),
 
            Section::make('Historical Outcome Range')
                ->columns(2)
                ->description('Used to derive the tier\'s baseline daily earning rate — see PackTier::baselineDailyRate().')
                ->schema([
                    TextInput::make('historical_outcome_min')->numeric()->suffix('%'),
                    TextInput::make('historical_outcome_max')->numeric()->suffix('%'),
                ]),
 
            Section::make('Marketing Features')
                ->schema([
                    // features is cast to `array` on the model, so TagsInput's
                    // JSON array of strings maps directly onto it — no
                    // transformation needed either direction.
                    TagsInput::make('features')
                        ->label('Feature bullet points')
                        ->placeholder('Type a feature and press Enter')
                        ->helperText('These render as the checklist on each pack card in Browse Packs. Order here is the display order.')
                        ->columnSpanFull(),
                ]),
 
            Section::make('Visibility')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active (purchasable)')
                        ->default(true)
                        ->helperText('Off hides this tier from Browse Packs entirely without deleting it — existing subscriptions on it are unaffected.'),
                ]),
            ]);
    }
}
