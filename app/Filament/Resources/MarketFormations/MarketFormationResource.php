<?php

namespace App\Filament\Resources\MarketFormations;

use App\Filament\Resources\MarketFormations\Pages\CreateMarketFormation;
use App\Filament\Resources\MarketFormations\Pages\EditMarketFormation;
use App\Filament\Resources\MarketFormations\Pages\ListMarketFormations;
use App\Filament\Resources\MarketFormations\Schemas\MarketFormationForm;
use App\Filament\Resources\MarketFormations\Tables\MarketFormationsTable;
use App\Models\MarketFormationStateModel;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MarketFormationResource extends Resource 
{
    protected static ?string $model = MarketFormationStateModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;
    protected static ?string $modelLabel = 'Market Formation State';
    protected static UnitEnum|string|null $navigationGroup = 'Intelligence';

    public static function form(Schema $schema): Schema
    {
        return MarketFormationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketFormationsTable::configure($table);
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
            'index' => ListMarketFormations::route('/'),
            'create' => CreateMarketFormation::route('/create'),
            'edit' => EditMarketFormation::route('/{record}/edit'),
        ];
    }
}
