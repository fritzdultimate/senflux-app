<?php

namespace App\Filament\Resources\PaymentSettings\Pages;

use App\Filament\Resources\PaymentSettings\MainWalletResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMainWallet extends CreateRecord
{
    protected static string $resource = MainWalletResource::class;
}
