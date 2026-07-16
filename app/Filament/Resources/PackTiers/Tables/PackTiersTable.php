<?php

namespace App\Filament\Resources\PackTiers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PackTiersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable(),
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('price')->money('USD')->sortable(),
                TextColumn::make('duration_days')->suffix(' days')->sortable(),
                TextColumn::make('slot_count')->label('Slots')->sortable(),
                TextColumn::make('min_capital_per_slot')->label('Min/Slot')->money('USD')->sortable(),
                TextColumn::make('max_capital_per_slot')->label('Max/Slot')->money('USD')
                    ->placeholder('No limit')->sortable(),
                TextColumn::make('historical_range')
                    ->label('Historical')
                    ->state(fn ($record) => $record->historical_outcome_min && $record->historical_outcome_max
                        ? "{$record->historical_outcome_min}–{$record->historical_outcome_max}%"
                        : '—'),
                TextColumn::make('subscriptions_count')
                    ->label('Subscribers')
                    ->counts('subscriptions')
                    ->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
