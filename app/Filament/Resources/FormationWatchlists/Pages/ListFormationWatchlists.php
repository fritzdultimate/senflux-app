<?php

namespace App\Filament\Resources\FormationWatchlists\Pages;

use App\Filament\Resources\FormationWatchlists\FormationWatchlistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFormationWatchlists extends ListRecords
{
    protected static string $resource = FormationWatchlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
