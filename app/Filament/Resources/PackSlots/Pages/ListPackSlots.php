<?php

namespace App\Filament\Resources\PackSlots\Pages;

use App\Filament\Resources\PackSlots\PackSlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackSlots extends ListRecords
{
    protected static string $resource = PackSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
