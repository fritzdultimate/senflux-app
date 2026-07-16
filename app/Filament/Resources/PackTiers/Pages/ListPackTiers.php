<?php

namespace App\Filament\Resources\PackTiers\Pages;

use App\Filament\Resources\PackTiers\PackTierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackTiers extends ListRecords
{
    protected static string $resource = PackTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
