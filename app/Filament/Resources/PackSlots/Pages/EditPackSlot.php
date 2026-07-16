<?php

namespace App\Filament\Resources\PackSlots\Pages;

use App\Filament\Resources\PackSlots\PackSlotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackSlot extends EditRecord
{
    protected static string $resource = PackSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
