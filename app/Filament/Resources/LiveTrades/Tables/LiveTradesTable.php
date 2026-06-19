<?php

namespace App\Filament\Resources\LiveTrades\Tables;

use App\Enums\TradeStatus;
use App\Enums\TradeType;
use App\Models\LiveTrade;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LiveTradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trackedAsset.symbol')->label('Asset')->weight('bold')->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state === TradeType::LONG ? 'success' : 'danger'),
                TextColumn::make('entry_price')->money('usd', decimalPlaces: 4),
                TextColumn::make('current_price')->money('usd', decimalPlaces: 4)->placeholder('—'),
                TextColumn::make('pnl_percent')
                    ->label('P&L %')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 2) . '%' : '—')
                    ->color(fn ($state) => $state === null ? 'gray' : ($state >= 0 ? 'success' : 'danger')),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state === TradeStatus::OPEN ? 'warning' : 'gray'),
                TextColumn::make('opened_at')->dateTime('M j, g:ia')->sortable(),
                TextColumn::make('closed_at')->dateTime('M j, g:ia')->placeholder('—')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open'   => 'Open',
                        'closed' => 'Closed',
                    ]),
                SelectFilter::make('tracked_asset_id')
                    ->label('Asset')
                    ->relationship('trackedAsset', 'symbol'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                    ->visible(fn (LiveTrade $record) => $record->status === TradeStatus::OPEN),

                    Action::make('closeTrade')
                        ->label('Close')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (LiveTrade $record) => $record->status === TradeStatus::OPEN)
                        ->schema([
                            TextInput::make('exit_price')
                                ->numeric()
                                ->prefix('$')
                                ->required(),
                        ])
                        ->action(function (LiveTrade $record, array $data) {
                            $record->close((float) $data['exit_price']);
                        })
                        ->requiresConfirmation()
                        ->successNotificationTitle('Trade closed'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('opened_at', 'desc');
    }
}
