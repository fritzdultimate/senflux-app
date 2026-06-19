<?php

namespace App\Filament\Resources\Deposits\Tables;

use App\Enums\DepositStatus;
use App\Models\Deposit;
use App\Services\DepositService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DepositsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Deposit $record) => $record->user->email),
                TextColumn::make('planConfig.label')
                    ->label('Plan')
                    ->badge(),
                TextColumn::make('amount_usd')
                    ->label('Requested')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('actually_paid_usd')
                    ->label('Paid')
                    ->money('usd')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('crypto_currency')
                    ->label('Currency')
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state instanceof DepositStatus ? $state->value : $state) {
                        'active' => 'success',
                        'confirmed' => 'info',
                        'confirming', 'waiting', 'pending' => 'warning',
                        'failed', 'expired'   => 'danger',
                        'finished' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ($state instanceof DepositStatus ? $state : DepositStatus::from($state))->label()),
                TextColumn::make('total_earnings')
                    ->label('Earned')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                TextColumn::make('activated_at')
                    ->dateTime('M j, Y H:i')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'waiting'    => 'Awaiting Payment',
                        'confirming' => 'Confirming',
                        'confirmed'  => 'Confirmed',
                        'active'     => 'Active',
                        'finished'   => 'Finished',
                        'failed'     => 'Failed',
                        'expired'    => 'Expired',
                        'refunded'   => 'Refunded',
                    ]),
                SelectFilter::make('plan_config_id')
                    ->relationship('planConfig', 'label')
                    ->label('Plan'),
                Filter::make('needs_attention')
                    ->label('Needs Attention')
                    ->query(fn (Builder $query) => $query->whereIn('status', ['pending', 'waiting', 'confirming']))
                    ->toggle(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->visible(fn (Deposit $record) => in_array(
                        $record->status instanceof DepositStatus ? $record->status->value : $record->status,
                        ['pending', 'waiting', 'confirming', 'confirmed']
                    ))
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('actually_paid_usd')
                            ->label('Actually Paid (USD)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(fn (Deposit $record) => $record->amount_usd),
                        TextInput::make('actually_paid')
                            ->label('Actually Paid (crypto amount)')
                            ->numeric()
                            ->default(fn (Deposit $record) => $record->crypto_amount),
                        Textarea::make('reason')
                            ->label('Reason for manual activation')
                            ->required()
                            ->minLength(10)
                            ->placeholder('e.g. Payment confirmed on-chain manually via Solscan, webhook did not fire.')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Deposit $record, array $data, DepositService $service) {
                        try {
                            $service->manualActivate(
                                deposit: $record,
                                admin: Auth::user(),
                                actuallyPaidUsd:  (float) $data['actually_paid_usd'],
                                actuallyPaid: isset($data['actually_paid']) ? (float) $data['actually_paid'] : null,
                                reason: $data['reason'],
                            );

                            Notification::make()
                                ->title('Deposit activated')
                                ->success()
                                ->body("Deposit #{$record->id} is now active and earning.")
                                ->send();
                        } catch (\RuntimeException $e) {
                            Notification::make()
                                ->title('Could not activate')
                                ->danger()
                                ->body($e->getMessage() === 'ALREADY_ACTIVE' ? 'This deposit is already active.' : 'An error occurred.')
                                ->send();
                        }
                    }),
                    ViewAction::make()
                ])
                ->label('Action')
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
