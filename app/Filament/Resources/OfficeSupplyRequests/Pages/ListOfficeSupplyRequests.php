<?php

namespace App\Filament\Resources\OfficeSupplyRequests\Pages;

use App\Filament\Resources\OfficeSupplyRequests\OfficeSupplyRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOfficeSupplyRequests extends ListRecords
{
    protected static string $resource = OfficeSupplyRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
