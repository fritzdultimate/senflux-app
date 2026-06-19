<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RankLevel;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('affiliate_code')->copyable(),
                TextColumn::make('rank')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ($state instanceof RankLevel ? $state : RankLevel::from($state ?? 'none'))->label())
                    ->color(fn ($state) => ($state instanceof RankLevel ? $state : RankLevel::from($state ?? 'none'))->order() > 5 ? 'warning' : 'gray'),
                TextColumn::make('subscription_plan')
                    ->badge()
                    ->placeholder('None')
                    ->color('info'),
                TextColumn::make('main_balance')
                    ->label('Main Balance')
                    ->money('usd')
                    ->state(fn (User $r) => $r->mainWallet()?->balance ?? 0),
                TextColumn::make('referrals_count')
                    ->label('Referrals')
                    ->counts('referrals'),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('kyc_verified_at')
                    ->label('KYC')
                    ->boolean()
                    ->getStateUsing(fn (User $r) => $r->kyc_verified_at !== null),
                TextColumn::make('created_at')->dateTime('M j, Y')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                SelectFilter::make('rank')
                    ->options(array_combine(
                        array_map(fn($r) => $r->value, RankLevel::cases()),
                        array_map(fn($r) => $r->label(), RankLevel::cases())
                    )),
                SelectFilter::make('subscription_plan')
                    ->options(['core' => 'Core', 'pro' => 'Pro', 'apex' => 'Apex']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
