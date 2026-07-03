<?php

namespace App\Filament\Resources\FormationWatchlists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FormationWatchlistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('mint_address')->required()->maxLength(64)
                ->helperText('The engine checks this every 5 minutes and auto-creates a Formation once it clears the minimum liquidity threshold.'),
                TextInput::make('token_symbol')->maxLength(20),
                Select::make('sector')->options([
                    'memecoins' => 'Memecoins', 'ai_agents' => 'AI Agents', 'defi' => 'DeFi',
                    'depin' => 'DePIN', 'gaming' => 'Gaming', 'rwa' => 'RWA', 'nft' => 'NFT', 'infrastructure' => 'Infrastructure',
                ])->native(false),
                Toggle::make('is_active')->default(true),
            ])->columns(2);
    }
}
