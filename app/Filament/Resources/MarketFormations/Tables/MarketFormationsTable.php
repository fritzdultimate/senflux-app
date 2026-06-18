<?php

namespace App\Filament\Resources\MarketFormations\Tables;

use App\Enums\BotDeploymentStatus;
use App\Enums\MarketFormationState;
use App\Models\MarketFormationStateModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MarketFormationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_current')
                    ->boolean()
                    ->label('Current'),
                TextColumn::make('state')
                    ->badge()
                    ->formatStateUsing(fn (MarketFormationState $state) => $state->label())
                    ->color(fn (MarketFormationState $state) => match($state) {
                        MarketFormationState::ACTIVE => 'success',
                        MarketFormationState::BUILDING => 'info',
                        MarketFormationState::EARLY => 'warning',
                        MarketFormationState::WEAKENING => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('bot_status')
                    ->badge()
                    ->formatStateUsing(fn (BotDeploymentStatus $state) => $state->label()),
                TextColumn::make('earnings_multiplier')
                ->getStateUsing(fn ($record) => number_format((float) $record->earnings_multiplier * 100, 0) . '%')
                    ->label('Payout Multiplier'),
                TextColumn::make('ecosystem')->badge()->color('gray'),
                TextColumn::make('setter.name')->label('Set By')->placeholder('System'),
                TextColumn::make('created_at')->dateTime('M j, Y H:i')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('makeCurrent')
                    ->label('Activate')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->visible(fn (MarketFormationStateModel $r) => !$r->is_current)
                    ->requiresConfirmation()
                    ->action(function (MarketFormationStateModel $record) {
                        MarketFormationStateModel::where('is_current', true)->update(['is_current' => false]);
                        $record->update(['is_current' => true]);
                        Notification::make()->title('Formation state activated')->success()->send();
                    }),
                ViewAction::make(),
                ])
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
