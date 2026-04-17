<?php

namespace App\Filament\Resources\OfficeSupplyCategories\Pages;

use App\Filament\Resources\OfficeSupplyCategories\OfficeSupplyCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOfficeSupplyCategory extends EditRecord
{
    protected static string $resource = OfficeSupplyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
