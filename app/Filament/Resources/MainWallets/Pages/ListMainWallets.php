<?php

namespace App\Filament\Resources\PaymentSettings\Pages;

use App\Filament\Resources\PaymentSettings\MainWalletResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMainWallets extends ListRecords
{
    protected static string $resource = MainWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
