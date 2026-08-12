<?php

namespace App\Filament\Resources\MainWallets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MainWalletsForm {
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('code')
                        ->default('usdt')
                        ->required(),
                    TextInput::make('currency')
                        ->label('Network')
                        ->default('USDTTRC20')
                        ->unique()
                        ->required(),
                    TextInput::make('label')
                        ->label('Label')
                        ->default('Tether')
                        ->required(),
                    Toggle::make('is_active')
                        ->label('Enable'),
                ]),
            ]);
    }
}
