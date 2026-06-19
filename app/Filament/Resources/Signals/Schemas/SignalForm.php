<?php

namespace App\Filament\Resources\Signals\Schemas;

use App\Enums\PlanType;
use App\Enums\SignalType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SignalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Signal')->columnSpanFull()->columns(2)->schema([
                    Select::make('tracked_asset_id')
                        ->label('Asset')
                        ->relationship('trackedAsset', 'symbol')
                        ->searchable()
                        ->required(),
                    Select::make('signal_type')
                        ->options(array_combine(
                            array_map(fn($t) => $t->value, SignalType::cases()),
                            array_map(fn($t) => $t->label(), SignalType::cases())
                        ))
                        ->required(),
                    TextInput::make('confidence_score')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('/100')
                        ->required(),
                    Select::make('min_plan')
                        ->label('Minimum Plan (gate)')
                        ->options(array_combine(
                            array_map(fn($p) => $p->value, PlanType::cases()),
                            array_map(fn($p) => $p->label(), PlanType::cases())
                        ))
                        ->nullable()
                        ->helperText('Leave empty to show to everyone, including non-subscribers.'),
                    DateTimePicker::make('expires_at')
                        ->nullable()
                        ->helperText('Leave empty for no expiry.'),
                    Textarea::make('note')
                        ->columnSpanFull()
                        ->rows(3),
                ]),
            ]);
    }
}
