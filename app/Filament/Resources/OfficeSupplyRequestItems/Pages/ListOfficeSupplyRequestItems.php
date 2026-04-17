<?php

namespace App\Filament\Resources\OfficeSupplyRequestItems\Pages;

use App\Filament\Resources\OfficeSupplyRequestItems\OfficeSupplyRequestItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOfficeSupplyRequestItems extends ListRecords
{
    protected static string $resource = OfficeSupplyRequestItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
