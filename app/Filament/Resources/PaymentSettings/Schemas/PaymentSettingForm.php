<?php

namespace App\Filament\Resources\PaymentSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('provider')
                        ->default('nowpayments')
                        // ->disabled()
                        ->dehydrated(),
                    TextInput::make('api_key')
                        ->label('API Key')
                        ->password()
                        ->required(),
                    TextInput::make('ipn_secret')
                        ->label('IPN / Webhook Secret')
                        ->password()
                        ->required(),
                    TextInput::make('webhook_url') 
                        ->default(fn () => route('webhooks.nowpayments'))
                        ->label('Webhook URL')
                        ->disabled()
                        ->dehydrated(),
                    Toggle::make('is_active')
                        ->label('Enable'),
                ]),
            ]);
    }
}
