<?php

namespace App\Filament\Resources\PaymentSettings;

use App\Filament\Resources\PaymentSettings\Pages\CreatePaymentSetting;
use App\Filament\Resources\PaymentSettings\Pages\EditPaymentSetting;
use App\Filament\Resources\PaymentSettings\Pages\ListPaymentSettings;
use App\Filament\Resources\PaymentSettings\Schemas\PaymentSettingForm;
use App\Filament\Resources\PaymentSettings\Tables\PaymentSettingsTable;
use App\Models\PaymentSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentSettingResource extends Resource
{
    protected static ?string $model = PaymentSetting::class;
    protected static ?string $navigationLabel = 'Payment Gateways';
    protected static ?string $modelLabel = 'Payment Settings';
    protected static ?string $pluralModelLabel = 'Payment Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    public static function form(Schema $schema): Schema
    {
        return PaymentSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canDelete(Model $record): bool {
        return false;
    }

    public static function canCreate(): bool {
        return PaymentSetting::count() < 2;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentSettings::route('/'),
            'create' => CreatePaymentSetting::route('/create'),
            'edit' => EditPaymentSetting::route('/{record}/edit'),
        ];
    }
}
