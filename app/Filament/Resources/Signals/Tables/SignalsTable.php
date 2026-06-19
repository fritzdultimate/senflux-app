<?php

namespace App\Filament\Resources\Signals\Tables;

use App\Enums\PlanType;
use App\Enums\SignalType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SignalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trackedAsset.symbol')->label('Asset')->weight('bold')->searchable(),
                TextColumn::make('signal_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => match ($state->value) {
                        'buy'   => 'success',
                        'sell'  => 'danger',
                        'watch' => 'warning',
                    }),
                TextColumn::make('confidence_score')->suffix('/100')->sortable(),
                TextColumn::make('min_plan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? 'Everyone')
                    ->color(fn ($state) => $state === null ? 'gray' : 'info'),
                TextColumn::make('expires_at')
                    ->dateTime('M j, g:ia')
                    ->placeholder('No expiry')
                    ->sortable(),
                TextColumn::make('created_at')->dateTime('M j, g:ia')->sortable(),
            ])
            ->filters([
                SelectFilter::make('signal_type')
                    ->options(array_combine(
                        array_map(fn($t) => $t->value, SignalType::cases()),
                        array_map(fn($t) => $t->label(), SignalType::cases())
                    )),
                SelectFilter::make('min_plan')
                    ->options(array_combine(
                        array_map(fn($p) => $p->value, PlanType::cases()),
                        array_map(fn($p) => $p->label(), PlanType::cases())
                    )),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
