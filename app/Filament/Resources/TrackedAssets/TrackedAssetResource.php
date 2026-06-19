<?php

namespace App\Filament\Resources\TrackedAssets;

use App\Filament\Resources\TrackedAssets\Pages\CreateTrackedAsset;
use App\Filament\Resources\TrackedAssets\Pages\EditTrackedAsset;
use App\Filament\Resources\TrackedAssets\Pages\ListTrackedAssets;
use App\Filament\Resources\TrackedAssets\Schemas\TrackedAssetForm;
use App\Filament\Resources\TrackedAssets\Tables\TrackedAssetsTable;
use App\Models\TrackedAsset;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrackedAssetResource extends Resource
{
    protected static ?string $model = TrackedAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;
    protected static UnitEnum|string|null $navigationGroup = 'Markets';

    public static function form(Schema $schema): Schema
    {
        return TrackedAssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrackedAssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrackedAssets::route('/'),
            'create' => CreateTrackedAsset::route('/create'),
            'edit' => EditTrackedAsset::route('/{record}/edit'),
        ];
    }
}
