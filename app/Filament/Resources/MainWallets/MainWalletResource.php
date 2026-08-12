<?php

namespace App\Filament\Resources\MainWallets;

use App\Filament\Resources\MainWallets\Pages\CreateMainWallet;
use App\Filament\Resources\MainWallets\Pages\EditMainWallet;
use App\Filament\Resources\MainWallets\Pages\ListMainWallets;
use App\Filament\Resources\MainWallets\Schemas\MainWalletsForm;
use App\Filament\Resources\MainWallets\Tables\MainWalletsTable;
use App\Models\MainWallet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MainWalletResource extends Resource {
    protected static ?string $model = MainWallet::class;
    protected static ?string $navigationLabel = 'Wallets';
    protected static ?string $modelLabel = 'Withdrawal Wallets';
    protected static ?string $pluralModelLabel = 'Withdrawal Wallets';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    public static function form(Schema $schema): Schema
    {
        return MainWalletsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MainWalletsTable::configure($table);
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
            'index' => ListMainWallets::route('/'),
            'create' => CreateMainWallet::route('/create'),
            'edit' => EditMainWallet::route('/{record}/edit'),
        ];
    }
}
