<?php

namespace App\Filament\Resources\LiveTrades\Pages;

use App\Filament\Resources\LiveTrades\LiveTradeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLiveTrade extends EditRecord
{
    protected static string $resource = LiveTradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
