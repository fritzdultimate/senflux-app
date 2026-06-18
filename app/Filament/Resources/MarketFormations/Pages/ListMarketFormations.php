<?php

namespace App\Filament\Resources\MarketFormations\Pages;

use App\Filament\Resources\MarketFormations\MarketFormationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMarketFormations extends ListRecords
{
    protected static string $resource = MarketFormationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
