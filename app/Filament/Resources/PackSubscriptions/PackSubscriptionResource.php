<?php

namespace App\Filament\Resources\PackSubscriptions;

use App\Filament\Resources\PackSubscriptions\Pages\CreatePackSubscription;
use App\Filament\Resources\PackSubscriptions\Pages\EditPackSubscription;
use App\Filament\Resources\PackSubscriptions\Pages\ListPackSubscriptions;
use App\Filament\Resources\PackSubscriptions\Schemas\PackSubscriptionForm;
use App\Filament\Resources\PackSubscriptions\Tables\PackSubscriptionsTable;
use App\Models\PackSubscription;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackSubscriptionResource extends Resource
{
    protected static ?string $model = PackSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = 'Packs';
 
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PackSubscriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackSubscriptionsTable::configure($table);
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
            'index' => ListPackSubscriptions::route('/'),
            'create' => CreatePackSubscription::route('/create'),
            'edit' => EditPackSubscription::route('/{record}/edit'),
        ];
    }
}
