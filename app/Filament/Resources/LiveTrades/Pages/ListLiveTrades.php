<?php

namespace App\Filament\Resources\LiveTrades\Pages;

use App\Filament\Resources\LiveTrades\LiveTradeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLiveTrades extends ListRecords
{
    protected static string $resource = LiveTradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
