<?php

namespace App\Filament\Resources\MarketFormations\Pages;

use App\Filament\Resources\MarketFormations\MarketFormationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMarketFormation extends EditRecord
{
    protected static string $resource = MarketFormationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
