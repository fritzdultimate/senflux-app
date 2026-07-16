<?php

namespace App\Filament\Resources\PackSubscriptions\Tables;

use App\Enums\PackSubscriptionStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PackSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->searchable()->sortable(),
                TextColumn::make('packTier.name')->label('Tier')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (PackSubscriptionStatus $state) => match ($state) {
                        PackSubscriptionStatus::ACTIVE => 'success',
                        PackSubscriptionStatus::IN_RENEWAL_WINDOW => 'warning',
                        PackSubscriptionStatus::EXPIRED, PackSubscriptionStatus::CLOSED => 'gray',
                        PackSubscriptionStatus::RENEWED => 'info',
                        PackSubscriptionStatus::REFUNDED => 'danger',
                    }),
                TextColumn::make('price_paid')->money('USD')->sortable(),
                TextColumn::make('slots_count')->label('Slots')->counts('slots'),
                TextColumn::make('purchased_at')->dateTime()->sortable(),
                TextColumn::make('matures_at')->dateTime()->sortable(),
                TextColumn::make('renewal_window_ends_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('upgradedFromTier.name')
                    ->label('Upgraded From')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(PackSubscriptionStatus::cases())->mapWithKeys(
                        fn (PackSubscriptionStatus $s) => [$s->value => ucfirst(str_replace('_', ' ', $s->value))]
                    )),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('purchased_at', 'desc');
    }
}
