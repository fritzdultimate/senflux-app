<?php

namespace App\Filament\Resources\TrackedAssets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TrackedAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asset')->columnSpanFull()->columns(2)->schema([
                    TextInput::make('symbol')->required()->maxLength(20)->uppercase(),
                    TextInput::make('name')->required()->maxLength(80),
                    TextInput::make('network')->maxLength(40),
                    TextInput::make('sort_order')->numeric()->default(0),
                    Toggle::make('is_active')->default(true),
                ]),

                Section::make('Price (read-only — synced by job)')->columnSpanFull()->columns(2)->schema([
                    TextInput::make('current_price')->numeric()->prefix('$')->disabled(),
                    TextInput::make('price_change_24h')->numeric()->suffix('%')->disabled(),
                ]),
            ]);
    }
}
