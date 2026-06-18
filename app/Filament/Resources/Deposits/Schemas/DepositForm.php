<?php

namespace App\Filament\Resources\Deposits\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepositForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Deposit Details')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Select::make('plan_config_id')
                            ->relationship('planConfig', 'label')
                            ->disabled(),
                        TextInput::make('amount_usd')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        TextInput::make('actually_paid_usd')
                            ->numeric()
                            ->prefix('$')
                            ->label('Actually Paid (USD)'),
                        TextInput::make('crypto_currency')
                            ->disabled(),
                        TextInput::make('crypto_amount')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('pay_address')
                            ->disabled()
                            ->columnSpanFull(),
                        Select::make('status')
                        ->options([
                            'pending'    => 'Pending',
                            'waiting'    => 'Awaiting Payment',
                            'confirming' => 'Confirming',
                            'confirmed'  => 'Confirmed',
                            'active'     => 'Active',
                            'finished'   => 'Finished',
                            'failed'     => 'Failed',
                            'expired'    => 'Expired',
                            'refunded'   => 'Refunded',
                        ])
                        ->required(),
                    TextInput::make('daily_rate')
                        ->numeric()
                        ->step(0.0001),
                    TextInput::make('total_earnings')
                        ->numeric()
                        ->disabled(),
                    ])
                    ->columns(2)
            ]);
    }
}
