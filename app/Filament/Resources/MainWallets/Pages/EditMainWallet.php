<?php

namespace App\Filament\Resources\MainWallets\Pages;

use App\Filament\Resources\MainWallets\MainWalletResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMainWallet extends EditRecord
{
    protected static string $resource = MainWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
