<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\RankLevel;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->email()->required(),
                    TextInput::make('phone'),
                    TextInput::make('country'),
                    TextInput::make('affiliate_code')->disabled(),
                    Select::make('referrer_id')
                        ->relationship('referrer', 'name')
                        ->searchable(),
                ])->columns(2),

                Section::make('Status')->schema([
                    Toggle::make('is_active'),
                    DateTimePicker::make('kyc_verified_at'),
                    Select::make('rank')
                        ->options(array_combine(
                            array_map(fn($r) => $r->value, RankLevel::cases()),
                            array_map(fn($r) => $r->label(), RankLevel::cases())
                        )),
                    Select::make('subscription_plan')
                        ->options(['core' => 'Core', 'pro' => 'Pro', 'apex' => 'Apex'])
                        ->nullable(),
                    DateTimePicker::make('subscription_expires_at'),
                ])->columns(2),
            ]);
    }
}
