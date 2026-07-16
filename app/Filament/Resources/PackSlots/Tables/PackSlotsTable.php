<?php

namespace App\Filament\Resources\PackSlots\Tables;

use App\Enums\PackSlotStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PackSlotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subscription.user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
 
                TextColumn::make('subscription.packTier.name')
                    ->label('Tier'),
 
                TextColumn::make('slot_number')->label('#')->sortable(),
 
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (PackSlotStatus $state) => match ($state) {
                        PackSlotStatus::EMPTY => 'gray',
                        PackSlotStatus::FUNDED => 'success',
                        PackSlotStatus::CLOSED => 'gray',
                    }),
 
                TextColumn::make('formation.token_symbol')
                    ->label('Formation')
                    ->placeholder('Not deployed')
                    ->searchable(),
 
                TextColumn::make('capital_amount')->label('Capital')->money('USD')->sortable(),
                TextColumn::make('realized_profit')->label('Profit')->money('USD')->sortable()
                    ->color(fn (?string $state) => $state && (float) $state > 0 ? 'success' : null),
 
                TextColumn::make('next_earning_at')
                    ->label('Next Payout')
                    ->dateTime()
                    ->sortable(),
 
                TextColumn::make('was_early_exit')
                    ->label('Early Exit')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Yes' : '—')
                    ->color(fn (bool $state) => $state ? 'danger' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
 
                TextColumn::make('funded_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('closed_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(PackSlotStatus::cases())->mapWithKeys(
                        fn (PackSlotStatus $s) => [$s->value => ucfirst($s->value)]
                    )),
                TernaryFilter::make('was_early_exit')->label('Early Exit'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
