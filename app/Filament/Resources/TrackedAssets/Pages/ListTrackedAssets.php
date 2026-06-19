<?php

namespace App\Filament\Resources\TrackedAssets\Pages;

use App\Filament\Resources\TrackedAssets\TrackedAssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrackedAssets extends ListRecords
{
    protected static string $resource = TrackedAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
