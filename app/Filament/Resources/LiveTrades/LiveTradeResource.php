<?php

namespace App\Filament\Resources\LiveTrades;

use App\Filament\Resources\LiveTrades\Pages\CreateLiveTrade;
use App\Filament\Resources\LiveTrades\Pages\EditLiveTrade;
use App\Filament\Resources\LiveTrades\Pages\ListLiveTrades;
use App\Filament\Resources\LiveTrades\Schemas\LiveTradeForm;
use App\Filament\Resources\LiveTrades\Tables\LiveTradesTable;
use App\Models\LiveTrade;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LiveTradeResource extends Resource {
    protected static ?string $model = LiveTrade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static UnitEnum|string|null $navigationGroup = 'Markets';

    public static function form(Schema $schema): Schema
    {
        return LiveTradeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LiveTradesTable::configure($table);
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
            'index' => ListLiveTrades::route('/'),
            'create' => CreateLiveTrade::route('/create'),
            'edit' => EditLiveTrade::route('/{record}/edit'),
        ];
    }
}
