<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('planConfig.label')->badge(),
                TextColumn::make('interval.value')->badge()->color('gray'),
                TextColumn::make('amount_paid')->money('usd'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($s) => match($s) {
                        'active'    => 'success',
                        'pending', 'waiting' => 'warning',
                        'expired'   => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('starts_at')->dateTime('M j, Y'),
                TextColumn::make('expires_at')->dateTime('M j, Y'),
                TextColumn::make('created_at')->dateTime('M j, Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'waiting' => 'Waiting',
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('activate')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->visible(fn (Subscription $r) => in_array($r->status, ['pending', 'waiting']))
                    ->requiresConfirmation()
                    ->action(function (Subscription $record, SubscriptionService $service) {
                        $service->activate($record);
                        Notification::make()->title('Subscription activated')->success()->send();
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
