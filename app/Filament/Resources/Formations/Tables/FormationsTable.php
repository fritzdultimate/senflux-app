<?php

namespace App\Filament\Resources\Formations\Tables;

use App\Enums\FormationState;
use App\Jobs\SyncFormationMarketData;
use App\Models\Formation;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FormationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('token_symbol')->weight('bold')->searchable(),
                TextColumn::make('token_name')
                    ->color('gray')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('state')
                    ->badge()
                    ->formatStateUsing(fn (FormationState $state) => $state->label())
                    ->color(fn (FormationState $state) => match ($state) {
                        FormationState::ACTIVE => 'success',
                        FormationState::BUILDING => 'info',
                        FormationState::EARLY => 'gray',
                        FormationState::MATURE => 'warning',
                        FormationState::WEAKENING => 'danger',
                        FormationState::IDLE => 'gray',
                    }),
                TextColumn::make('pair_url')
                    ->label('Pair')
                    ->formatStateUsing(fn ($state) => $state ? \Illuminate\Support\Str::limit(str_replace(['https://', 'www.'], '', $state), 30) : '—')
                    ->url(fn ($record) => $record->pair_url, shouldOpenInNewTab: true)
                    ->color('gray'),
                TextColumn::make('score')->suffix('/100')->sortable(),
                TextColumn::make('confidence'),
                TextColumn::make('liquidity_usd')
                    ->label('Liquidity')
                    ->money('USD')
                    ->sortable(),
 
                TextColumn::make('volume_24h')
                    ->label('24h Vol')
                    ->money('USD')
                    ->sortable(),
 
                TextColumn::make('price_change_24h')
                    ->label('24h %')
                    ->suffix('%')
                    ->color(fn (?float $state) => $state === null ? 'gray' : ($state >= 0 ? 'success' : 'danger'))
                    ->sortable(),
 
                TextColumn::make('deployedSlots_count')
                    ->label('Deployed Slots')
                    ->counts('deployedSlots')
                    ->sortable(),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('auto_managed')
                    ->boolean()
                    ->label('Auto')
                    ->toggleable(isToggledHiddenByDefault: true),
 
                TextColumn::make('market_data_synced_at')
                    ->label('Synced')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('state_changed_at')->since()->label('Last Change'),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->options(collect(FormationState::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    self::deploySlotAction(),
                    self::logEventAction(),
                    Action::make('sync')
                        ->label('Sync Formation')
                        ->icon('heroicon-o-arrow-down-circle')
                        ->color('primary')
                        ->action(function (Formation $record, array $data) {
                            SyncFormationMarketData::dispatch($record);

                            Notification::make()
                                ->title('Sync queued')
                                ->body("{$record->token_symbol} market data will refresh shortly.")
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Formation $record) => $record->mint_address !== null)
                        ->requiresConfirmation()
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('score', 'desc');
    }

     /**
     * The manual stand-in for the automated matching engine you don't
     * have yet. Only appears when the formation actually accepts new
     * deployments (ACTIVE), and only lists slots that are genuinely
     * eligible — funded, undeployed.
     */
    protected static function deploySlotAction(): Action {
        return Action::make('deploy')
            ->label('Deploy Slot')
            ->icon('heroicon-o-arrow-down-circle')
            ->color('primary')
            ->visible(fn (Formation $record) => $record->state->acceptsNewDeployments())
            ->form([
                Select::make('slot_id')
                    ->label('Eligible Slot')
                    ->options(function () {
                        return \App\Models\PackSlot::query()
                            ->where('status', \App\Enums\PackSlotStatus::FUNDED->value)
                            ->whereNull('formation_id')
                            ->with('subscription.user')
                            ->get()
                            ->mapWithKeys(fn ($slot) => [
                                $slot->id => "#{$slot->slot_number} — {$slot->subscription->user->name} — \${$slot->capital_amount}",
                            ]);
                    })
                    ->required()
                    ->searchable(),
            ])
            ->action(function (Formation $record, array $data) {
                $slot = \App\Models\PackSlot::findOrFail($data['slot_id']);
                app(\App\Services\FormationDeploymentService::class)->deploy($slot, $record);
            })
            ->requiresConfirmation();
    }

     /**
     * Manual event logging — stands in for the detection engine calling
     * FormationEventLogger directly. Lets you populate the live ticker
     * with realistic signal events (capital concentration, wallet
     * clusters, liquidity) while there's no automated feed.
     */
    protected static function logEventAction(): Action {
        return Action::make('logEvent')
            ->label('Log Signal')
            ->icon('heroicon-o-bolt')
            ->color('gray')
            ->form([
                Select::make('type')
                    ->options([
                        \App\Enums\FormationEventType::CAPITAL_CONCENTRATION->value => 'Capital Concentration Detected',
                        \App\Enums\FormationEventType::WALLET_CLUSTER->value => 'Wallet Cluster Identified',
                        \App\Enums\FormationEventType::LIQUIDITY_INCREASING->value => 'Liquidity Increasing',
                        \App\Enums\FormationEventType::EXPOSURE_REDUCED->value => 'Exposure Reduced',
                    ])
                    ->required()->native(false),
                TextInput::make('message')
                    ->placeholder('Leave blank to use the default message')
                    ->maxLength(150),
            ])
            ->action(function (Formation $record, array $data) {
                app(\App\Services\FormationEventLogger::class)->log(
                    $record,
                    \App\Enums\FormationEventType::from($data['type']),
                    $data['message'] ?: null,
                );
            });
    }
}
