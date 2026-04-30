<?php

namespace App\Filament\Resources\OfficeSupplyItemVariants\Pages;

use App\Filament\Resources\OfficeSupplyItemVariants\OfficeSupplyItemVariantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOfficeSupplyItemVariants extends ListRecords
{
    protected static string $resource = OfficeSupplyItemVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
