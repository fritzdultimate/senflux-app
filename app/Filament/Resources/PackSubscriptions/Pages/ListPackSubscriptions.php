<?php

namespace App\Filament\Resources\PackSubscriptions\Pages;

use App\Filament\Resources\PackSubscriptions\PackSubscriptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackSubscriptions extends ListRecords
{
    protected static string $resource = PackSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
