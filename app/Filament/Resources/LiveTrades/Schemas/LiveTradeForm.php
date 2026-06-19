<?php

namespace App\Filament\Resources\LiveTrades\Schemas;

use App\Enums\TradeType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LiveTradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Trade')->columnSpanFull()->columns(2)->schema([
                Select::make('tracked_asset_id')
                    ->label('Asset')
                    ->relationship('trackedAsset', 'symbol')
                    ->searchable()
                    ->required(),
                Select::make('type')
                    ->options(array_combine(
                        array_map(fn($t) => $t->value, TradeType::cases()),
                        array_map(fn($t) => $t->label(), TradeType::cases())
                    ))
                    ->required(),
                TextInput::make('entry_price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                DateTimePicker::make('opened_at')
                    ->required()
                    ->default(now()),
            ]),
            ]);
    }
}
