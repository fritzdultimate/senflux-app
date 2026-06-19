<?php

namespace App\Filament\Resources\WalletTransactions\Tables;

use App\Enums\TransactionType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WalletTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('wallet.type')
                    ->badge()
                    ->formatStateUsing(fn ($s) => ($s instanceof \App\Enums\WalletType ? $s : \App\Enums\WalletType::from($s))->label()),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($s) => ($s instanceof TransactionType ? $s : TransactionType::from($s))->label())
                    ->color(fn ($s) => ($s instanceof TransactionType ? $s : TransactionType::from($s))->isCredit() ? 'success' : 'danger'),
                TextColumn::make('amount')
                    ->money('usd')
                    ->formatStateUsing(function ($record) {
                        $sign = $record->is_debit ? '-' : '+';
                        return $sign . '$' . number_format((float) $record->amount, 2);
                    }),
                TextColumn::make('balance_after')->money('usd')->label('Balance After'),
                TextColumn::make('description')->limit(40),
                TextColumn::make('created_at')->dateTime('M j, Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(array_combine(
                        array_map(fn($t) => $t->value, TransactionType::cases()),
                        array_map(fn($t) => $t->label(), TransactionType::cases())
                    )),
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
