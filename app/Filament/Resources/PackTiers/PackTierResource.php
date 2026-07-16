<?php

namespace App\Filament\Resources\PackTiers;

use App\Filament\Resources\PackTiers\Pages\CreatePackTier;
use App\Filament\Resources\PackTiers\Pages\EditPackTier;
use App\Filament\Resources\PackTiers\Pages\ListPackTiers;
use App\Filament\Resources\PackTiers\Schemas\PackTierForm;
use App\Filament\Resources\PackTiers\Tables\PackTiersTable;
use App\Models\PackTier;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackTierResource extends Resource
{
    protected static ?string $model = PackTier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static UnitEnum|string|null $navigationGroup = 'Packs';
 
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PackTierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackTiersTable::configure($table);
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
            'index' => ListPackTiers::route('/'),
            'create' => CreatePackTier::route('/create'),
            'edit' => EditPackTier::route('/{record}/edit'),
        ];
    }
}
