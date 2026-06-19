<?php

namespace App\Filament\Resources\TrackedAssets\Pages;

use App\Filament\Resources\TrackedAssets\TrackedAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrackedAsset extends EditRecord
{
    protected static string $resource = TrackedAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
