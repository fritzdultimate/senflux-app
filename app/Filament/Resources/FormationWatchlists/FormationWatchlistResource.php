<?php

namespace App\Filament\Resources\FormationWatchlists;

use App\Filament\Resources\FormationWatchlists\Pages\CreateFormationWatchlist;
use App\Filament\Resources\FormationWatchlists\Pages\EditFormationWatchlist;
use App\Filament\Resources\FormationWatchlists\Pages\ListFormationWatchlists;
use App\Filament\Resources\FormationWatchlists\Schemas\FormationWatchlistForm;
use App\Filament\Resources\FormationWatchlists\Tables\FormationWatchlistsTable;
use App\Models\FormationWatchlistItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FormationWatchlistResource extends Resource
{
    protected static ?string $model = FormationWatchlistItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;
    protected static string|UnitEnum|null $navigationGroup = 'Intelligence Engine';
    protected static ?string $navigationLabel = 'Watchlist';

    public static function form(Schema $schema): Schema
    {
        return FormationWatchlistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormationWatchlistsTable::configure($table);
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
            'index' => ListFormationWatchlists::route('/'),
            'create' => CreateFormationWatchlist::route('/create'),
            'edit' => EditFormationWatchlist::route('/{record}/edit'),
        ];
    }
}
