<?php

namespace App\Filament\Resources\OfficeSupplyCategories\Pages;

use App\Filament\Resources\OfficeSupplyCategories\OfficeSupplyCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOfficeSupplyCategories extends ListRecords
{
    protected static string $resource = OfficeSupplyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
