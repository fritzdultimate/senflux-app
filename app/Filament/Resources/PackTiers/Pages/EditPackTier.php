<?php

namespace App\Filament\Resources\PackTiers\Pages;

use App\Filament\Resources\PackTiers\PackTierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackTier extends EditRecord
{
    protected static string $resource = PackTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
