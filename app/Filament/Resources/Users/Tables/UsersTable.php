<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RankLevel;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Filament\Resources\WalletTransactions\WalletTransactionResource;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\WalletService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('affiliate_code')->copyable(),
                TextColumn::make('rank')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ($state instanceof RankLevel ? $state : RankLevel::from($state ?? 'none'))->label())
                    ->color(fn ($state) => ($state instanceof RankLevel ? $state : RankLevel::from($state ?? 'none'))->order() > 5 ? 'warning' : 'gray'),
                TextColumn::make('subscription_plan')
                    ->badge()
                    ->placeholder('None')
                    ->color('info'),
                TextColumn::make('main_balance')
                    ->label('Main Balance')
                    ->money('usd')
                    ->state(fn (User $r) => $r->mainWallet()?->balance ?? 0),
                TextColumn::make('referrals_count')
                    ->label('Referrals')
                    ->counts('referrals'),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('kyc_verified_at')
                    ->label('KYC')
                    ->boolean()
                    ->getStateUsing(fn (User $r) => $r->kyc_verified_at !== null),
                TextColumn::make('created_at')->dateTime('M j, Y')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                SelectFilter::make('rank')
                    ->options(array_combine(
                        array_map(fn($r) => $r->value, RankLevel::cases()),
                        array_map(fn($r) => $r->label(), RankLevel::cases())
                    )),
                SelectFilter::make('subscription_plan')
                    ->options(['core' => 'Core', 'pro' => 'Pro', 'apex' => 'Apex']),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('fund')
                        ->label('Fund')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->schema([
                            Select::make('wallet_type')
                                ->label('Wallet')
                                ->options(array_combine(
                                    array_map(fn($w) => $w->value, WalletType::cases()),
                                    array_map(fn($w) => $w->label(), WalletType::cases())
                                ))
                                ->default(WalletType::MAIN->value)
                                ->required(),
                            TextInput::make('amount')
                                ->numeric()
                                ->minValue(0.01)
                                ->required()
                                ->prefix('$'),
                            Textarea::make('reason')
                                ->label('Reason')
                                ->required()
                                ->minLength(10)
                                ->placeholder('Why are you crediting this user? (required, min 10 chars)'),
                        ])
                        ->action(function (User $record, array $data) {
                            app(WalletService::class)->credit(
                                user: $record,
                                walletType: WalletType::from($data['wallet_type']),
                                amount: (float) $data['amount'],
                                type: TransactionType::ADJUSTMENT,
                                description: "Admin credit: {$data['reason']}",
                                createdBy: Auth::id(),
                            );
    
                            \App\Models\ActivityLog::record(
                                action:      'admin_fund_user',
                                description: "Credited \${$data['amount']} to {$record->name}'s {$data['wallet_type']} wallet — {$data['reason']}",
                                subject:     $record,
                                meta:        ['amount' => $data['amount'], 'wallet' => $data['wallet_type']],
                            );
                        })
                        ->successNotificationTitle('Wallet funded successfully')
                        ->requiresConfirmation(),

                    Action::make('debit')
                        ->label('Debit')
                        ->icon('heroicon-o-minus-circle')
                        ->color('danger')
                        ->schema([
                            Select::make('wallet_type')
                                ->label('Wallet')
                                ->options(array_combine(
                                    array_map(fn($w) => $w->value, WalletType::cases()),
                                    array_map(fn($w) => $w->label(), WalletType::cases())
                                ))
                                ->default(WalletType::MAIN->value)
                                ->required(),
                            TextInput::make('amount')
                                ->numeric()
                                ->minValue(0.01)
                                ->required()
                                ->prefix('$'),
                            Textarea::make('reason')
                                ->label('Reason')
                                ->required()
                                ->minLength(10)
                                ->placeholder('Why are you debiting this user? (required, min 10 chars)'),
                        ])
                        ->action(function (User $record, array $data) {
                            app(WalletService::class)->debit(
                                user:          $record,
                                walletType:    WalletType::from($data['wallet_type']),
                                amount:        (float) $data['amount'],
                                type:          TransactionType::ADJUSTMENT,
                                description:   "Admin debit: {$data['reason']}",
                                createdBy:     Auth::id(),
                            );
    
                            \App\Models\ActivityLog::record(
                                action:      'admin_debit_user',
                                description: "Debited \${$data['amount']} from {$record->name}'s {$data['wallet_type']} wallet — {$data['reason']}",
                                subject:     $record,
                                meta:        ['amount' => $data['amount'], 'wallet' => $data['wallet_type']],
                            );
                        })
                        ->successNotificationTitle('Wallet debited successfully')
                        ->requiresConfirmation(),

                    Action::make('verifyKyc')
                        ->label('Verify KYC')
                        ->icon('heroicon-o-shield-check')
                        ->color('info')
                        ->visible(fn (User $record) => $record->kyc_verified_at === null)
                        ->action(function (User $record) {
                            $record->update(['kyc_verified_at' => now()]);
    
                            ActivityLog::record(
                                action: 'admin_verify_kyc',
                                description: "Verified KYC for {$record->name}",
                                subject: $record,
                            );
                        })
                        ->successNotificationTitle('KYC verified')
                        ->requiresConfirmation(),

                     Action::make('toggleActive')
                        ->label(fn (User $record) => $record->is_active ? 'Suspend' : 'Reactivate')
                        ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                        ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                        ->schema(fn (User $record) => $record->is_active ? [
                            Textarea::make('reason')
                                ->label('Reason for suspension')
                                ->required()
                                ->minLength(10),
                        ] : [])
                        ->action(function (User $record, array $data) {
                            $record->update(['is_active' => !$record->is_active]);
    
                            ActivityLog::record(
                                action: $record->is_active ? 'admin_reactivate_user' : 'admin_suspend_user',
                                description: $record->is_active
                                    ? "Reactivated {$record->name}"
                                    : "Suspended {$record->name} — " . ($data['reason'] ?? 'No reason given'),
                                subject: $record,
                            );
                        })
                        ->requiresConfirmation(),

                    Action::make('resetTwoFactor')
                        ->label('Reset 2FA')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->visible(fn (User $record) => $record->two_factor_enabled)
                        ->action(function (User $record) {
                            $record->update(['two_factor_enabled' => false]);
    
                            ActivityLog::record(
                                action:      'admin_reset_2fa',
                                description: "Reset 2FA for {$record->name}",
                                subject:     $record,
                            );
                        })
                        ->successNotificationTitle('Two-factor authentication reset')
                        ->requiresConfirmation(),

                    Action::make('viewLedger')
                        ->label('Ledger')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->url(fn (User $record) => WalletTransactionResource::getUrl('index', [
                            'tableFilters' => ['user_id' => ['value' => $record->id]],
                        ])),

                
                ])
            ])
             ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
