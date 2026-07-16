<?php

namespace App\Filament\Resources\PackSlots;

use App\Filament\Resources\PackSlots\Pages\CreatePackSlot;
use App\Filament\Resources\PackSlots\Pages\EditPackSlot;
use App\Filament\Resources\PackSlots\Pages\ListPackSlots;
use App\Filament\Resources\PackSlots\Schemas\PackSlotForm;
use App\Filament\Resources\PackSlots\Tables\PackSlotsTable;
use App\Models\PackSlot;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackSlotResource extends Resource
{
    protected static ?string $model = PackSlot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static UnitEnum|string|null $navigationGroup = 'Packs';
 
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PackSlotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackSlotsTable::configure($table);
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
            'index' => ListPackSlots::route('/'),
            'create' => CreatePackSlot::route('/create'),
            'edit' => EditPackSlot::route('/{record}/edit'),
        ];
    }
}
