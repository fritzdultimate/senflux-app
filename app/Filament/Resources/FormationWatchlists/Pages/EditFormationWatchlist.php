<?php

namespace App\Filament\Resources\FormationWatchlists\Pages;

use App\Filament\Resources\FormationWatchlists\FormationWatchlistResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFormationWatchlist extends EditRecord
{
    protected static string $resource = FormationWatchlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
