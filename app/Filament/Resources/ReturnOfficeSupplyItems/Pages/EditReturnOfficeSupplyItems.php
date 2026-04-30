<?php

namespace App\Filament\Resources\ReturnOfficeSupplyItems\Pages;

use App\Filament\Resources\ReturnOfficeSupplyItems\ReturnOfficeSupplyItemsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReturnOfficeSupplyItems extends EditRecord
{
    protected static string $resource = ReturnOfficeSupplyItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
