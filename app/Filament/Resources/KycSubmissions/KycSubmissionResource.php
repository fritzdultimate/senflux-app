<?php

namespace App\Filament\Resources\KycSubmissions;

use App\Enums\KycStatus;
use App\Filament\Resources\KycSubmissions\Pages\ListKycSubmissions;
use App\Filament\Resources\KycSubmissions\Tables\KycSubmissionsTable;
use App\Models\KycSubmission;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KycSubmissionResource extends Resource
{
    protected static ?string $model = KycSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $recordTitleAttribute = 'KYC Submissions';
    protected static UnitEnum|string|null $navigationGroup = 'Compliance';
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return KycSubmissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array {
        return [
            'index' => ListKycSubmissions::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::where('status', KycStatus::PENDING->value)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string {
        return 'warning';
    }
}
