<?php

namespace App\Filament\Resources\PackSubscriptions\Pages;

use App\Filament\Resources\PackSubscriptions\PackSubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackSubscription extends EditRecord
{
    protected static string $resource = PackSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
