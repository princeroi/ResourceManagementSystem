<?php

namespace App\Filament\Resources\OfficeSupplyItems\Pages;

use App\Filament\Resources\OfficeSupplyItems\OfficeSupplyItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOfficeSupplyItems extends ListRecords
{
    protected static string $resource = OfficeSupplyItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
