<?php

namespace App\Filament\Resources\KycSubmissions\Pages;

use App\Filament\Resources\KycSubmissions\KycSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListKycSubmissions extends ListRecords
{
    protected static string $resource = KycSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        // No CreateAction — submissions only ever originate from the user-facing upload flow.
        return [];
    }
}
