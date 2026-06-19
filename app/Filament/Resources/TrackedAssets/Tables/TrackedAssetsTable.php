<?php

namespace App\Filament\Resources\TrackedAssets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrackedAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('symbol')->searchable()->weight('bold'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('network')->badge()->color('gray'),
                TextColumn::make('current_price')
                    ->money('usd')
                    ->placeholder('Not synced'),
                TextColumn::make('price_change_24h')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 2) . '%' : '—')
                    ->color(fn ($state) => $state === null ? 'gray' : ($state >= 0 ? 'success' : 'danger')),
                TextColumn::make('price_updated_at')
                    ->dateTime('M j, g:ia')
                    ->placeholder('Never')
                    ->sortable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('sort_order')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
