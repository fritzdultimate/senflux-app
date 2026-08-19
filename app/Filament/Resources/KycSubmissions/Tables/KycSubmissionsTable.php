<?php

namespace App\Filament\Resources\KycSubmissions\Tables;

use App\Enums\KycStatus;
use App\Enums\KycTier;
use App\Models\ActivityLog;
use App\Models\KycSubmission;
use App\Services\KycService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class KycSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->description(fn (KycSubmission $r) => $r->user->email),
                TextColumn::make('tier')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ($state instanceof KycTier ? $state : KycTier::from($state))->label())
                    ->color(fn ($state) => ($state instanceof KycTier ? $state : KycTier::from($state))->color()),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ($state instanceof KycStatus ? $state : KycStatus::from($state))->label())
                    ->color(fn ($state) => ($state instanceof KycStatus ? $state : KycStatus::from($state))->color()),
                TextColumn::make('id_document_type')
                    ->label('Document')
                    ->formatStateUsing(fn ($state) => $state ? ucwords(str_replace('_', ' ', $state)) : '—'),
                TextColumn::make('submitted_at')->dateTime('M j, Y H:i')->sortable(),
                TextColumn::make('reviewer.name')->label('Reviewed By')->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->label('User'),
                SelectFilter::make('status')
                    ->options(array_combine(
                        array_map(fn ($s) => $s->value, KycStatus::cases()),
                        array_map(fn ($s) => $s->label(), KycStatus::cases()),
                    )),
                SelectFilter::make('tier')
                    ->options(array_combine(
                        array_map(fn ($t) => $t->value, KycTier::cases()),
                        array_map(fn ($t) => $t->label(), KycTier::cases()),
                    )),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    Action::make('viewDocuments')
                        ->label('Review Documents')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color('gray')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->schema(function (KycSubmission $record) {
                            $rows = [];

                            $rows[] = "<div style='margin-bottom:8px'><strong>Document type:</strong> " . e(ucwords(str_replace('_', ' ', $record->id_document_type ?? '—'))) . '</div>';
                            $rows[] = "<div style='margin-bottom:8px'><strong>Document number:</strong> " . e($record->id_document_number ?? '—') . '</div>';

                            foreach ($record->documentFields() as $field => $label) {
                                $url = URL::temporarySignedRoute(
                                    'kyc.documents.show',
                                    now()->addMinutes(5),
                                    ['submission' => $record->id, 'field' => $field],
                                );
                                $rows[] = "<div style='margin-bottom:6px'><a href=\"{$url}\" target=\"_blank\" rel=\"noopener\" style=\"color:#8B7CF6;text-decoration:underline\">{$label} &rarr;</a></div>";
                            }

                            return [
                                Placeholder::make('documents')
                                    ->label('')
                                    ->content(new HtmlString(implode('', $rows))),
                            ];
                        }),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (KycSubmission $record) => $record->status === KycStatus::PENDING)
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('note')
                                ->label('Internal note (optional)')
                                ->placeholder('Optional note for the audit trail.'),
                        ])
                        ->action(function (KycSubmission $record, array $data, KycService $service) {
                            try {
                                $service->approve($record, Auth::id(), $data['note'] ?? null);

                                Notification::make()
                                    ->title('KYC approved')
                                    ->success()
                                    ->body("{$record->user->name}'s {$record->tier->label()} verification was approved.")
                                    ->send();
                            } catch (\RuntimeException $e) {
                                Notification::make()
                                    ->title('Could not approve')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (KycSubmission $record) => $record->status === KycStatus::PENDING)
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('reason')
                                ->label('Rejection reason')
                                ->required()
                                ->minLength(10)
                                ->placeholder('Explain what was wrong so the user can resubmit correctly (required, min 10 chars).'),
                        ])
                        ->action(function (KycSubmission $record, array $data, KycService $service) {
                            try {
                                $service->reject($record, Auth::id(), $data['reason']);

                                Notification::make()
                                    ->title('KYC rejected')
                                    ->success()
                                    ->body("{$record->user->name}'s {$record->tier->label()} verification was rejected.")
                                    ->send();
                            } catch (\RuntimeException $e) {
                                Notification::make()
                                    ->title('Could not reject')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ])->label('Action'),
            ]);
    }
}
